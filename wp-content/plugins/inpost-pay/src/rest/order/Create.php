<?php

namespace Ilabs\Inpost_Pay\rest\order;

use Exception;
use Ilabs\Inpost_Pay\Integration\Shipping\ShippingMethodIntegrationFactory;
use Ilabs\Inpost_Pay\Lib\Attribution\OrderAttribution;
use Ilabs\Inpost_Pay\Lib\Authentication\AuthenticationFactory;
use Ilabs\Inpost_Pay\Lib\Authentication\Credentials;
use Ilabs\Inpost_Pay\Lib\exception\CantCreateAttribution;
use Ilabs\Inpost_Pay\Lib\exception\CantGetOrderObjectException;
use Ilabs\Inpost_Pay\Lib\exception\EmptyCredentialsForOrderAuthenticationException;
use Ilabs\Inpost_Pay\Lib\exception\InvalidAuthenticationType;
use Ilabs\Inpost_Pay\Lib\exception\UserNotFoundException;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\hooks\OrderUpdate;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\WooCommerceBasketCache;
use Ilabs\Inpost_Pay\WooCommerceOrder;
use JsonException;
use WC_Data_Exception;

class Create extends Base {
	public function __construct() {
		$this->restricted = true;
	}

	protected function describe() {
		$this->post['/inpost/v1/izi/order'] = function ( $request ) {

			$this->checkSignature( $request );
			OrderUpdate::$block = true;
			$data               = $request->get_body();
			die( mb_convert_encoding( $this->handleRequest( $data ), 'UTF-8' ) );
		};
	}

	/**
	 * @throws JsonException
	 */
	private function handleRequest( $data ) {
		define( 'DOING_AJAX', true );
		Logger::response( $data );
		$data = json_decode( $data, false, 512, JSON_THROW_ON_ERROR );
		InPostIzi::blockPut();
		remove_all_actions( 'woocommerce_cart_loaded_from_session' );
		if ( class_exists( \InspireLabs\WoocommerceInpost\EasyPack::class ) ) {
			add_filter( 'woocommerce_shipping_packages', function ( $packages ) {
				remove_filter( 'woocommerce_shipping_packages',
					[ \InspireLabs\WoocommerceInpost\EasyPack::EasyPack(), 'woocommerce_shipping_packages' ],
					1000 );

				return $packages;
			}, 900 );
		}

		WooCommerceBasketCache::restore( $data->order_details->basket_id, true );
		try {
			[ $redir, $oid, $order ] = $this->createOrder( $data );
		} catch ( WC_Data_Exception|Exception $e ) {
			Logger::log('Order not created: ' . $e->getMessage());
			return [ 'error' => $e->getMessage() ];
		}

		if ( $order !== null ) {
			CartSession::setOrderToCart( $data->order_details->basket_id, $oid, $redir );
			try {
				$wooOrderResponse = WooCommerceOrder::getOrder( $oid, $order )->encode();
			} catch ( CantGetOrderObjectException $e ) {
				Logger::log('Order not created: ' . $e->getMessage());
				return [ 'error' => $e->getMessage() ];
			}

			\WC()->cart->empty_cart();
			do_action( 'inpost_pay_order_created', $oid, $data );
			Logger::response( $wooOrderResponse );


			return $wooOrderResponse;
		}

		return [ 'error' => 'Order not created' ];
	}


	/**
	 * @throws WC_Data_Exception
	 * @throws InvalidAuthenticationType
	 * @throws Exception
	 */
	private function createOrder( $data ): array {
		$deliveryMethod  = esc_attr( get_option( 'izi_transport_method_' . strtolower( $data->delivery->delivery_type ) ) );
		$parcelMachineId = property_exists( $data->delivery,
			'delivery_point' ) ? $data->delivery->delivery_point : null;

		$shippingMethodIntegration = ShippingMethodIntegrationFactory::create( $deliveryMethod,
			$parcelMachineId );

		$shippingMethodIntegration->configure();

		$authenticator = AuthenticationFactory::create( 'order' );

		$credentials = new Credentials();

		$credentials->set_email( $data->account_info->mail );
		$credentials->set_phone_number( $data->account_info->phone_number->phone );

		try {
			$user = $authenticator->authenticate( $credentials );
			if ( $user && $user->ID ) {
				WC()->session->set( 'customer_id', $user->ID );
			}
		} catch ( UserNotFoundException|EmptyCredentialsForOrderAuthenticationException $e ) {
			$user = null;
		}

		$billingAddress = [
			'first_name' => $data->account_info->name,
			'last_name'  => $data->account_info->surname,
			'email'      => $data->account_info->mail,
			'phone'      => $data->account_info->phone_number->country_prefix . ' ' . $data->account_info->phone_number->phone,
			'address_1'  => $data->account_info->client_address->address,
			'address_2'  => '',
			'city'       => $data->account_info->client_address->city,
			'state'      => '',
			'postcode'   => $data->account_info->client_address->postal_code,
			'country'    => $data->account_info->client_address->country_code,
		];

		$shippingAddress = $billingAddress;
		if ( isset( $data->delivery->delivery_address ) ) {
			$deliveryNameParts = explode( ' ', $data->delivery->delivery_address->name );
			$deliveryName      = count( $deliveryNameParts ) > 1 ? array_shift( $deliveryNameParts ) : '';
			$deliverySurname   = implode( ' ', $deliveryNameParts );
			$shippingAddress   = [
				'first_name' => $deliveryName,
				'last_name'  => $deliverySurname,
				'email'      => $data->delivery->mail,
				'phone'      => $data->delivery->phone_number->country_prefix . ' ' . $data->delivery->phone_number->phone,
				'address_1'  => $data->delivery->delivery_address->address,
				'address_2'  => $data->delivery->courier_note ?? '',
				'city'       => $data->delivery->delivery_address->city,
				'state'      => '',
				'postcode'   => $data->delivery->delivery_address->postal_code,
				'country'    => $data->delivery->delivery_address->country_code,
			];
		}

		if ( empty( WC()->customer->get_shipping_country() ) ) {
			WC()->customer->set_shipping_country( 'PL' );
		}
		if ( empty( WC()->customer->get_shipping_postcode() ) ) {
			WC()->customer->set_shipping_postcode( $data->account_info->client_address->postal_code );
		}
		if ( empty( WC()->customer->get_shipping_city() ) ) {
			WC()->customer->set_shipping_city( $data->account_info->client_address->city );
		}

		$shipping_packages = WC()->cart->get_shipping_packages();

		$calculate_shipping = WC()->shipping()->calculate_shipping( $shipping_packages );

		unset( WC()->session->chosen_shipping_methods );
		WC()->session->set( 'chosen_shipping_methods', array( $deliveryMethod ) );
		WC()->session->set( 'shipping_method_counts', [ 0 => count( $calculate_shipping[0]['rates'] ) ] );


		add_filter( 'woocommerce_shipping_chosen_method', function ( $default, $rates, $chosen_method ) use ( $deliveryMethod ) {
			return $deliveryMethod;
		} );
		$checkout = WC()->checkout();


		$order_id = $checkout->create_order( [
			'billing_email'   => $data->account_info->mail,
			'payment_method'  => 'inpost-izi',
			'shipping_method' => $deliveryMethod,
			'shipping'        => $shippingAddress,
			'billing'         => $billingAddress,
			'is_vat_exempt'   => 'no',
		] );

		//$checkout->create_order_shipping_lines( $order, wc()->session->get( 'chosen_shipping_methods' ), wc()->shipping()->get_packages() );


		if ( $order_id instanceof \WP_Error ) {
			Logger::debug( 'Create order error:' );
			Logger::log( $order_id );

			return [ null, null, null ];
		}

		if ( did_action( 'woocommerce_after_register_post_type' ) ) {
			$order = wc_get_order( $order_id );
		} else {
			$order = new \WC_Order( $order_id );
		}

		if ( $user ) {
			$order->set_customer_id( $user->ID );
		}


		if ( version_compare( WC()->version, '8.6', '>' ) ) {
			try {
				$order_attribution = new OrderAttribution( $data->order_details->basket_id );
				$order_attribution->add_to_order( $order );
			} catch ( CantCreateAttribution|JsonException $e ) {
			}
		}


		$order->save();

		if ( ! ( $order instanceof \WC_Order ) ) {
			return [ null, null, null ];
		}


		$shippingMethodIntegration->setWcOrder( $order );

		$additionalDeliveryOptionsPrice = 0.0;
		if ( isset( $data->delivery->delivery_codes ) && is_array( $data->delivery->delivery_codes ) ) {
			foreach ( $data->delivery->delivery_codes as $additionalDeliveryOption ) {
				if ( 'PWW' === $additionalDeliveryOption || 'COD' === $additionalDeliveryOption ) {
					$additionalDeliveryOptionsPrice += (float) str_replace( ',',
						'.',
						esc_attr( get_option( 'izi_transport_price_' . strtolower( $additionalDeliveryOption ) ) ) );
				}
			}

		}

		$shippingMethodIntegration->filterTotal(
			function ( $total ) use ( $additionalDeliveryOptionsPrice
			) {
				return $total + $additionalDeliveryOptionsPrice;
			}
		);

		if ( isset( $data->invoice_details ) ) {
			foreach ( (array) $data->invoice_details as $name => $value ) {
				$order->update_meta_data( 'impost_invoice_' . $name, $value );
			}

			$billingAddress = [
				'company'      => $data->invoice_details->legal_form == 'PERSON' ? '' : $data->invoice_details->company_name,
				'first_name'   => $data->invoice_details->legal_form == 'PERSON' ? $data->invoice_details->name : '',
				'last_name'    => $data->invoice_details->legal_form == 'PERSON' ? $data->invoice_details->surname : '',
				'email'        => $data->invoice_details->mail,
				'phone'        => $data->account_info->phone_number->country_prefix . ' ' . $data->account_info->phone_number->phone,
				'address_1'    => $data->invoice_details->street . ' ' . ( $data->invoice_details->building ?? '' ) . ' ' . ( $data->invoice_details->flat ?? '' ),
				'address_2'    => '',
				'city'         => $data->invoice_details->city,
				'state'        => '',
				'postcode'     => $data->invoice_details->postal_code,
				'country'      => $data->invoice_details->country_code,
				'invoice_note' => $this->invoiceNote( $data->invoice_details ),
			];

			do_action( 'inpostpay_invoice_details', $order, $data->invoice_details );
		}
		if ( isset( $data->delivery->delivery_codes ) ) {
			$order->update_meta_data( 'delivery_codes', implode( ',', $data->delivery->delivery_codes ) );
		}

		$order->update_meta_data( 'origin_phone_number', json_encode( $data->account_info->phone_number ) );
		$order->update_meta_data( '_easypack_send_method', ( $data->delivery->delivery_type == 'APM' ? 'parcel_machine' : 'courier' ) );
		$order->update_meta_data( 'inpost_account_info', serialize( $data->account_info ) );
		$order->update_meta_data( 'inpost_consents', serialize( $data->consents ) );

		$order->set_address( $billingAddress, 'billing' );
		$order->set_address( $shippingAddress, 'shipping' );

		// add payment method
		$order->set_payment_method_title( 'Inpost Pay' );

		// order status
		$order->set_status( 'wc-on-hold', 'Zamówienie Inpost Pay' );

		$order->set_customer_note( isset( $data->order_details->order_comments ) ? $data->order_details->order_comments : '' );

		if ( isset( $data->delivery->delivery_point ) ) {
			$order->update_meta_data( 'delivery_point', $data->delivery->delivery_point );
			$order->update_meta_data( 'parcel_machine_id', $data->delivery->delivery_point );
		}
		$order->update_meta_data( 'izi_payment_type', $data->order_details->payment_type );


		$order->update_taxes();
		$order->calculate_totals( false );
		$order->save();

		$order_received_url = wc_get_endpoint_url( 'order-received', $order->get_id(), wc_get_checkout_url() . '?showIzi=true&key=' . $order->get_order_key() );

		return [ $order_received_url, $order->get_id(), $order ];
	}

	private function invoiceNote( $invoiceDetails ): string {
		$invoiceNote = '';
		if ( isset( $invoiceDetails->additional_information ) ) {
			$invoiceNote = $invoiceDetails->additional_information . ' \n ';
		}
		if ( isset( $invoiceDetails->tax_id ) ) {
			$invoiceNote .= __( 'Tax id', 'inpost-pay' ) . ':';
			$invoiceNote .= ( $invoiceDetails->tax_id_prefix ?? ' ' ) . ' ' . $invoiceDetails->tax_id;
		}

		return $invoiceNote;
	}
}
