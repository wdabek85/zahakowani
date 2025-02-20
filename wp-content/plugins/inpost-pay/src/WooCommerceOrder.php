<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\BasketIdentification;
use Ilabs\Inpost_Pay\Integration\Basket\Quantity\QuantityIntegrationFactory;
use Ilabs\Inpost_Pay\Lib\exception\CantGetOrderObjectException;
use Ilabs\Inpost_Pay\Lib\helpers\CacheHelper;
use Ilabs\Inpost_Pay\Lib\helpers\HPOSHelper;
use Ilabs\Inpost_Pay\Lib\item\order\AccountInfo;
use Ilabs\Inpost_Pay\Lib\item\order\ClientAddress;
use Ilabs\Inpost_Pay\Lib\item\order\Delivery;
use Ilabs\Inpost_Pay\Lib\item\order\DeliveryAddress;
use Ilabs\Inpost_Pay\Lib\item\order\InvoiceDetails;
use Ilabs\Inpost_Pay\Lib\item\order\Order;
use Ilabs\Inpost_Pay\Lib\item\order\OrderDetails;
use Ilabs\Inpost_Pay\Lib\item\order\Phone;
use Ilabs\Inpost_Pay\Lib\item\order\PhoneNumber;
use Ilabs\Inpost_Pay\Lib\item\Price;
use Ilabs\Inpost_Pay\Lib\item\Product;
use Ilabs\Inpost_Pay\Lib\item\ProductAttribute;
use Ilabs\Inpost_Pay\Lib\item\Quantity;
use Ilabs\Inpost_Pay\Lib\item\Variant;
use Ilabs\Inpost_Pay\models\CartSession;

class WooCommerceOrder extends IziJsonResponse {
	private $orderId;
	private $order;

	private $orderBasePriceNet = 0.0;
	private $orderBasePriceGross = 0.0;
	private $orderBasePriceVat = 0.0;

	private $orderPromoPriceNet = 0.0;
	private $orderPromoPriceGross = 0.0;
	private $orderPromoPriceVat = 0.0;

	private HPOSHelper $HPOSHelper;

	public function __construct( $orderId, $order = null ) {
		$this->orderId = $orderId;
		if ( $order === null ) {
			$this->order = wc_get_order( $this->orderId );
		} else {
			$this->order = $order;
		}

		$this->HPOSHelper = new HPOSHelper( $this->order );

	}

	public function getOrderObject() {
		return $this->order;
	}

	/**
	 * @throws CantGetOrderObjectException
	 */
	public static function getOrder( $orderId, $order = null ): Order {
		$wooCommerceOrder = new self( $orderId, $order );
		$order            = new Order();
		CacheHelper::disableWPCache();
		if ( ! $wooCommerceOrder->getOrderObject() ) {
			throw new CantGetOrderObjectException( $orderId);
		}

		$order->account_info    = $wooCommerceOrder->mapAccountInfo();
		$order->invoice_details = $wooCommerceOrder->mapInvoiceDetails();
		$order->delivery        = $wooCommerceOrder->mapDelivery();
		$order->products        = $wooCommerceOrder->mapProducts();
		$order->order_details   = $wooCommerceOrder->mapOrderDetails();


		$consents        = unserialize( $wooCommerceOrder->HPOSHelper->get_meta( 'inpost_consents' ) );
		$order->consents = $consents ?: $wooCommerceOrder->mapConsents();

		return $order;
	}

	public function mapAccountInfo() {
		$data = [];
		try {
			$data = unserialize( get_post_meta( $this->orderId, 'inpost_account_info', true ) );
			if ( empty( $data ) || empty( $data->phone_number ) ) {
				$data = unserialize( $this->HPOSHelper->get_meta( 'inpost_account_info' ) );
			}
		} catch ( \Exception $e ) {

		}
		$accountInfo = new AccountInfo();

		$phoneNumber                 = new PhoneNumber();
		$phoneNumber->country_prefix = isset( $data, $data->phone_number, $data->phone_number->country_prefix ) && $data->phone_number->country_prefix ? $data->phone_number->country_prefix : '';
		$phoneNumber->phone          = isset( $data, $data->phone_number, $data->phone_number->phone ) ? $data->phone_number->phone : '';

		$clientAddress               = new ClientAddress();
		$clientAddress->country_code = isset( $data, $data->client_address, $data->client_address->country_code ) ? $data->client_address->country_code : 'PL';
		$clientAddress->address      = $data->client_address->address ?: '';
		$clientAddress->city         = $data->client_address->city ?: '';
		$clientAddress->postal_code  = $data->client_address->postal_code ?: '';

		$accountInfo->name           = $data->name ?: '';
		$accountInfo->surname        = $data->surname ?: '';
		$accountInfo->phone_number   = $phoneNumber ?: '';
		$accountInfo->mail           = $data->mail ?: '';
		$accountInfo->client_address = $clientAddress;

		return $accountInfo;
	}

	public function mapProducts(): array {
		$array = [];

		foreach ( $this->order->get_items() as $cartContent ) {
			array_push( $array, $this->mapCartProduct( $cartContent ) );
		}

		return $array;
	}

	public function mapCartProduct( $item ) {
		$product = $this->mapProductData( $item );

		$product->quantity    = $this->readQuantity( $item );
		$product->base_price  = $this->readCartProductPromoPrice( $item );
		$product->promo_price = $this->readCartProductPromoPrice( $item );

		$product->base_price = $product->promo_price;

		return $product;
	}

	public function readCartProductPromoPrice( $item ): Price {
		$productSimple = $item->get_product();
		$quantity      = $item->get_quantity();

		if ( $productSimple->is_type( 'compositepro' ) ) {
			//Store YES or NO
			$compositepro_per_item_shipping = get_post_meta( $productSimple->get_id(), '_compositepro_per_item_shipping' );

			$compositepro_per_item_pricing = get_post_meta( $productSimple->get_id(), '_compositepro_per_item_pricing' );

			if ( $compositepro_per_item_pricing === 'no' && $compositepro_per_item_shipping === 'no' ) {
				$price = new Price();

				$priceIncludingTax = wc_get_price_including_tax( $productSimple );
				$priceExcludingTax = wc_get_price_excluding_tax( $productSimple );
				$vat               = $priceIncludingTax - $priceExcludingTax;

				$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
				$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
				$price->vat   = number_format( $vat, 2, '.', '' );

				$this->orderPromoPriceNet   += $priceExcludingTax * $quantity;
				$this->orderPromoPriceGross += $priceIncludingTax * $quantity;
				$this->orderPromoPriceVat   += $vat * $quantity;

				return $price;
			}

		}

		$data           = $item->get_data();
		$format_decimal = array( 'subtotal', 'subtotal_tax', 'total', 'total_tax', 'tax_total', 'shipping_tax_total' );

		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			if ( isset( $data[ $key ] ) ) {
				$data[ $key ] = number_format( $data[ $key ], 2, '.', '' );
			}
		}

		if ( ! empty( $data['taxes']['total'] ) ) {
			$taxes = array();

			foreach ( $data['taxes']['total'] as $tax_rate_id => $tax ) {
				$taxes[] = array(
					'id'       => $tax_rate_id,
					'total'    => $tax,
					'subtotal' => isset( $data['taxes']['subtotal'][ $tax_rate_id ] ) ? $data['taxes']['subtotal'][ $tax_rate_id ] : '',
				);
			}
			$data['taxes'] = $taxes;
		} elseif ( isset( $data['taxes'] ) ) {
			$data['taxes'] = array();
		}

		$price = new Price();

		$quantity        = $item->get_quantity();
		$productQuantity = ( new QuantityIntegrationFactory() )->create( $productSimple );

		if ( $productQuantity->get_quantity_type() === 'DECIMAL' ) {
			$priceIncludingTax = ( $item->get_total() + $item->get_total_tax() );
			$priceExcludingTax = $item->get_total();
			$vat               = $item->get_total_tax();
		} else {
			$priceIncludingTax = $quantity ? ( $item->get_total() + $item->get_total_tax() ) / $quantity : 0;
			$priceExcludingTax = $item->get_total();
			$vat               = $item->get_total_tax();
		}


		$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
		$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
		$price->vat   = number_format( $vat, 2, '.', '' );

		$this->orderPromoPriceNet   += $priceExcludingTax;
		$this->orderPromoPriceGross += $priceIncludingTax;
		$this->orderPromoPriceVat   += $vat;

		return $price;
	}

	public function readCartProductBasePrice( $item ): Price {
		$productSimple = $item->get_product();
		$quantity      = $item->get_quantity();
		$price         = new Price();

		$priceIncludingTax = wc_get_price_including_tax( $productSimple, [ "price" => $productSimple->get_regular_price() ] );
		$priceExcludingTax = wc_get_price_excluding_tax( $productSimple, [ "price" => $productSimple->get_regular_price() ] );
		$vat               = $priceIncludingTax - $priceExcludingTax;

		$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
		$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
		$price->vat   = number_format( $vat, 2, '.', '' );

		$this->orderBasePriceNet   += $priceExcludingTax * $quantity;
		$this->orderBasePriceGross += $priceIncludingTax * $quantity;
		$this->orderBasePriceVat   += $vat * $quantity;

		return $price;
	}

	public function readQuantity( $item ): Quantity {
		$quantity           = $this->readStockQuantity( $item->get_product() );
		$quantity->quantity = $item->get_quantity();

		return $quantity;
	}

	public function readStockQuantity( $productSimple ): Quantity {
		$quantity = new Quantity();

		$productQuantity = ( new QuantityIntegrationFactory() )->create( $productSimple );

		$quantity->quantity_type = $productQuantity->get_quantity_type();

		$availableQuantity = $productSimple->get_stock_quantity();
		if ( $availableQuantity ) {
			$quantity->available_quantity = $availableQuantity;
		} else {
			$quantity->available_quantity = 999;
		}

		$maxQuantity = $productSimple->get_max_purchase_quantity();
		if ( $maxQuantity !== - 1 ) {
			$quantity->max_quantity = $maxQuantity;
		} else {
			$quantity->max_quantity = 999;
		}

		return $quantity;
	}

	public function mapProductData( $cartItem ) {
		if ( ! $cartItem->get_product() ) {
			return;
		}
		$product = new Product();

		$product->product_id = $cartItem->get_product_id();
		if ( isset( $cartItem->get_product()->get_category_ids()[0] ) ) {
			$product->product_category = $cartItem->get_product()->get_category_ids()[0];
		}
		$product->ean                 = $cartItem->get_product()->get_sku() ?: '0';
		$product->product_name        = strip_tags( html_entity_decode( $cartItem->get_product()->get_name() ) );
		$product->product_description = strip_shortcodes( strip_tags( $cartItem->get_product()->get_description() ) );
		$product->product_link        = $cartItem->get_product()->get_permalink();

		$wcProduct = $cartItem->get_product();
		$image     = wp_get_attachment_image_src( get_post_thumbnail_id( $wcProduct->get_id() ), 'single-post-thumbnail' );
		if ( ! $image && $wcProduct->get_parent_id() ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $wcProduct->get_parent_id() ), 'single-post-thumbnail' );
		}

		if ( $image && $image[0] ) {
			$product->product_image = $image[0];
		} else {
			$product->product_image = '';
		}

		$product->additional_product_images = $this->getProductImages( $cartItem->get_product() );

		$product->variants           = $this->mapProductVariables( $cartItem->get_product() );
		$product->product_attributes = $this->mapProductAttributes( $cartItem );

		return $product;
	}


	public function getProductImages( $wcProduct ): array {
		$images = [];
		$gallery_image_ids = $wcProduct->get_gallery_image_ids();

		if ( ! empty( $gallery_image_ids ) ) {
			foreach ( $gallery_image_ids as $gallery_image_id ) {
				$image = new \stdClass();
				$gallery_image_small = wp_get_attachment_image_src( $gallery_image_id, 'thumbnail' );
				$gallery_image_normal = wp_get_attachment_image_src( $gallery_image_id, 'full' );

				if ( $gallery_image_small && $gallery_image_small[0] && $gallery_image_normal && $gallery_image_normal[0] ) {
					$image->small_size = str_replace( "http://", "https://", $gallery_image_small[0] );
					$image->normal_size = str_replace( "http://", "https://", $gallery_image_normal[0] );
				}

				$images[] = $image;
			}
		}

		return array_slice($images, 0, 10);
	}

	public function mapProductAttributes( \WC_Order_Item $item ): array {
		$array      = [];
		$hideprefix = '_';

		$formatted_meta    = array();
		$include_all       = false;
		$meta_data         = $item->get_meta_data();
		$hideprefix_length = strlen( $hideprefix );
		$product           = is_callable( array( $this, 'get_product' ) ) ? $this->get_product() : false;
		$order_item_name   = $item->get_name();

		foreach ( $meta_data as $meta ) {
			if ( empty( $meta->id ) || '' === $meta->value || ! is_scalar( $meta->value ) || ( $hideprefix_length && substr( $meta->key, 0, $hideprefix_length ) === $hideprefix ) ) {
				continue;
			}

			$meta->key     = rawurldecode( (string) $meta->key );
			$meta->value   = rawurldecode( (string) $meta->value );
			$attribute_key = str_replace( 'attribute_', '', $meta->key );
			$display_key   = wc_attribute_label( $attribute_key, $product );
			$display_value = wp_kses_post( $meta->value );

			if ( taxonomy_exists( $attribute_key ) ) {
				$term = get_term_by( 'slug', $meta->value, $attribute_key );
				if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name ) {
					$display_value = $term->name;
				}
			}

			// Skip items with values already in the product details area of the product name.
			if ( ! $include_all && $product && $product->is_type( 'variation' ) && wc_is_attribute_in_product_name( $display_value, $order_item_name ) ) {
				continue;
			}

			if ( strlen( strip_tags( $display_value ) ) > 1 ) {
				$array[] = $this->mapProductAttribute( $display_key, $display_value );
			}
		}

		return $array;
	}

	public function mapProductAttribute( $name, $value ): ProductAttribute {
		return new ProductAttribute( $name, $value );
	}

	public function mapProductVariables( $productSimple ): array {
		$array = [];

		if ( $productSimple->get_parent_id() ) {
			$productSimple = wc_get_product( $productSimple->get_parent_id() );
		}

		foreach ( $productSimple->get_attributes() as $attribute ) {
			if ( $attribute->get_visible() && $attribute->get_variation() === true ) {
				array_push( $array, $this->mapProductVariable( $attribute ) );
			}
		}

		return $array;
	}

	public function mapProductVariable( $attribute ): Variant {
		$variant = new Variant();

		$variant->variant_id     = $attribute->get_id();
		$variant->variant_name   = $attribute->get_name();
		$variant->variant_values = implode( ", ", $attribute->get_options() );

		$variant->variant_description = "";
		$variant->variant_type        = "";

		return $variant;
	}

	public function mapClientAddress(): ClientAddress {
		$clientAddress = new ClientAddress();

		$clientAddress->country_code = $this->order->get_billing_country();
		$clientAddress->address      = $this->order->get_billing_address_1() . " " . $this->order->get_billing_address_2();
		$clientAddress->city         = $this->order->get_billing_city();
		$clientAddress->postal_code  = $this->order->get_billing_postcode();

		return $clientAddress;
	}

	public function mapInvoiceDetails(): InvoiceDetails {
		$invoiceDetails = new InvoiceDetails();

		$legalForm = $this->HPOSHelper->get_meta( 'impost_invoice_legal_form' );
		if ( $legalForm ) {
			$invoiceDetails->legal_form = $legalForm;
		}
		$invoiceDetails->country_code             = $this->HPOSHelper->get_meta( 'impost_invoice_country_code' ) ?: '';
		$invoiceDetails->tax_id_prefix            = $this->HPOSHelper->get_meta( 'impost_invoice_tax_id_prefix' ) ?: '';
		$invoiceDetails->tax_id                   = $this->HPOSHelper->get_meta( 'impost_invoice_tax_id' ) ?: '';
		$invoiceDetails->company_name             = $this->HPOSHelper->get_meta( 'impost_invoice_company_name' ) ?: '';
		$invoiceDetails->name                     = $this->HPOSHelper->get_meta( 'impost_invoice_name' ) ?: '';
		$invoiceDetails->surname                  = $this->HPOSHelper->get_meta( 'impost_invoice_surname' ) ?: '';
		$invoiceDetails->city                     = $this->HPOSHelper->get_meta( 'impost_invoice_city' ) ?: '';
		$invoiceDetails->street                   = $this->HPOSHelper->get_meta( 'impost_invoice_street' ) ?: '';
		$invoiceDetails->building                 = $this->HPOSHelper->get_meta( 'impost_invoice_building' ) ?: '';
		$invoiceDetails->flat                     = $this->HPOSHelper->get_meta( 'impost_invoice_flat' ) ?: '';
		$invoiceDetails->postal_code              = $this->HPOSHelper->get_meta( 'impost_invoice_postal_code' ) ?: '';
		$invoiceDetails->mail                     = $this->HPOSHelper->get_meta( 'impost_invoice_mail' ) ?: '';
		$invoiceDetails->registration_data_edited = $this->HPOSHelper->get_meta( 'registration_data_edited' ) ?: '';
		$invoiceDetails->additional_information   = $this->HPOSHelper->get_meta( 'impost_invoice_additional_information' ) ?: '';

		return $invoiceDetails;
	}

	public function mapDelivery(): Delivery {
		$delivery = new Delivery();

		$deliveryCodes = explode( ',', $this->HPOSHelper->get_meta( 'delivery_codes' ) );

		$additionalDeliveryOptionDictionary = [
			'PWW' => 'Paczka w Weekend',
			'COD' => 'Pobranie'
		];
		$additionalDeliveryOptionsName      = [];
		foreach ( $deliveryCodes as $code ) {
			if ( ! $code ) {
				continue;
			}
			$net                             = floatval( str_replace( ',', '.', esc_attr( get_option( 'izi_transport_price_' . strtolower( $code ) ) ) ) );
			$gross                           = $net * WooDeliveryPrice::getShippingTaxModifier();
			$additionalDeliveryOptionsName[] = $additionalDeliveryOptionDictionary[ $code ];
			$delivery->delivery_options      = [
				[
					"delivery_name"         => $additionalDeliveryOptionDictionary[ $code ],
					"delivery_code_value"   => $code,
					'delivery_option_price' => [
						"net"   => $net,
						"gross" => number_format( $gross, 2, '.', '' ),
						"vat"   => number_format( $gross - $net, 2, '.', '' )
					],
				]
			];
		}

		//todo skorzystać z detdeliveryparameters

		$delivery->delivery_type = $this->HPOSHelper->get_meta( '_easypack_send_method', true ) == 'parcel_machine' ? 'APM' : 'COURIER';
		$this->setDeliveryPrice( $delivery );

		$delivery->mail             = $this->order->get_billing_email();
		$delivery->phone            = $this->mapPhone( $this->order->get_shipping_phone() );
		$delivery->delivery_address = $this->mapDeliveryAddress();

		$delivery->courier_note = $this->order->get_shipping_address_2();

		$deliveryPoint = $this->HPOSHelper->get_meta( 'delivery_point' );
		if ( $deliveryPoint ) {
			$delivery->delivery_point = $deliveryPoint;
		}

		return $delivery;
	}

	private function setDeliveryPrice( &$deliveryObject ) {
		$wooDeliveryPrice = new WooDeliveryPrice();
		$delivery         = $wooDeliveryPrice->mapDelivery( $this->order );

		foreach ( $delivery as $option ) {
			if ( $deliveryObject->delivery_type == $option->delivery_type ) {
				$deliveryObject->delivery_price = $option->delivery_price;
				$deliveryObject->delivery_date  = $option->delivery_date;
			}
		}
	}

	public function mapDeliveryAddress(): DeliveryAddress {
		$deliveryAddress = new DeliveryAddress();

		$deliveryAddress->name         = $this->order->get_shipping_first_name() . " " . $this->order->get_shipping_last_name();
		$deliveryAddress->country_code = $this->order->get_shipping_country();
		$deliveryAddress->address      = $this->order->get_shipping_address_1();
		$deliveryAddress->city         = $this->order->get_shipping_city();
		$deliveryAddress->postal_code  = $this->order->get_shipping_postcode();

		return $deliveryAddress;
	}

	public function mapPhone( $telephoneNumber ): Phone {
		$phone = new Phone();

		$trigPhone             = $this->readPhone();
		$phone->country_prefix = $trigPhone[0];
		$phone->phone          = $trigPhone[1];

		return $phone;
	}

	public function mapPhoneNumber(): PhoneNumber {
		$phoneNumber = new PhoneNumber();

		$trigPhone                   = $this->readPhone();
		$phoneNumber->country_prefix = $trigPhone[0];
		$phoneNumber->phone          = $trigPhone[1];

		return $phoneNumber;
	}

	public function readPhone(): array {
		$orderPhone = $this->HPOSHelper->get_meta( 'origin_phone_number' );
		if ( $orderPhone ) {
			$orderPhone = json_decode( $orderPhone );

			return [ $orderPhone->country_prefix, $orderPhone->phone ];
		}

		$array = explode( " ", $this->order->get_billing_phone() );

		return [ array_shift( $array ), implode( " ", $array ) ];
	}

	private function readComments(): string {
		return $this->order->get_customer_note();
	}

	public function mapOrderDetails(): OrderDetails {
		$orderDetails = new OrderDetails();

		$orderDetails->order_comments    = $this->readComments();
		$orderDetails->order_id          = $this->orderId;
		$orderDetails->customer_order_id = $this->orderId;
		$orderDetails->pos_id            = esc_attr( get_option( 'izi_pos_id' ) );
		if ( ! $orderDetails->pos_id ) {
			$orderDetails->pos_id = '0';
		}
		$orderDetails->order_creation_date = date( "Y-m-d\TH:i:s.000\Z", strtotime( $this->order->get_date_created() ) );
		$orderDetails->order_update_date   = date( "Y-m-d\TH:i:s.000\Z", strtotime( $this->order->get_date_modified() ) );
		$orderDetails->merchant_id         = esc_attr( get_option( 'izi_client_id' ) );

		$status                       = $this->order->get_status();
		$status_labels                = get_option( 'izi_status_map' );
		$orderDetails->payment_status = $this->HPOSHelper->get_meta( 'izi_payment_status' );
		$orderDetails->order_status   = $this->HPOSHelper->get_meta( 'izi_order_status' );

		$trackingNumber                                  = $this->HPOSHelper->get_meta( '_easypack_parcel_tracking' );
		$status_description                              = ( ! empty( $status_labels[ 'wc-' . $status ] ) ) ? $status_labels[ 'wc-' . $status ] : $status;
		$orderDetails->order_merchant_status_description = $status_description;

		$orderDetails->order_base_price  = $this->readSummaryOrderPromoPrice();
		$orderDetails->order_discount    = $this->readOrderDiscountTotal();
		$orderDetails->order_final_price = $this->readSummaryOrderFinalPrice();

		$orderDetails->delivery_references_list = [ $trackingNumber ];
		$orderDetails->currency                 = $this->order->get_currency();
		$orderDetails->payment_type             = $this->readPaymentType();
		$orderDetails->basket_id                = CartSession::getCartIdByOrderId( $this->orderId );

		return $orderDetails;
	}

	public function readSummaryOrderFinalPrice(): Price {
		$price = new Price();

		$price->gross = number_format( $this->order->get_total(), 2, '.', '' );
		$price->net   = number_format( $this->order->get_total() - $this->order->get_total_tax(), 2, '.', '' );
		$price->vat   = number_format( $this->order->get_total_tax(), 2, '.', '' );

		return $price;
	}

	public function readSummaryOrderPromoPrice(): Price {
		$price = new Price();

		$price->gross = number_format( $this->order->get_total() - $this->order->get_shipping_total() - $this->order->get_shipping_tax(), 2, '.', '' );
		$price->net   = number_format( $this->order->get_total() - $this->order->get_total_tax() - $this->order->get_shipping_total(), 2, '.', '' );
		$price->vat   = number_format( $this->order->get_total_tax() - $this->order->get_shipping_tax(), 2, '.', '' );

		//        $price->gross = number_format($this->orderPromoPriceGross, 2, '.', '');
		//        $price->net = number_format($this->orderPromoPriceNet, 2, '.', '');
		//        $price->vat = number_format($this->orderPromoPriceVat, 2, '.', '');

		return $price;
	}

	public function readOrderDiscountTotal(): string {
		$discountTotal = (float) $this->order->get_discount_total( false ) + (float) $this->order->get_discount_tax( false );
		$discountTotal = number_format( $discountTotal, 2, '.', '' );

		return $discountTotal;
	}

	public function readSummaryOrderBasePrice(): Price {
		$price = new Price();

		$price->gross = number_format( $this->orderBasePriceGross, 2, '.', '' );
		$price->net   = number_format( $this->orderBasePriceNet, 2, '.', '' );
		$price->vat   = number_format( $this->orderBasePriceVat, 2, '.', '' );

		return $price;
	}

	public function getBasketHash() {
		$basket = ( new \Ilabs\Inpost_Pay\Lib\Remote( $this->orderId ) )->basketGet();
		if ( isset( $basket->summary ) ) {
			return $basket->summary->basket_hash;
		}

		return "";
	}

	public function readPaymentType() {
		return $this->HPOSHelper->get_meta( 'izi_payment_type' );
	}
}
