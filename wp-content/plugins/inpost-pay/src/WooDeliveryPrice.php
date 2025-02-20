<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\Integration\Shipping\WoocommerceInpostIntegration;
use Ilabs\Inpost_Pay\Lib\item\Price;
use WC_Shipping_Method;
use WC_Shipping_Rate;
use WC_Shipping_Zones;
use WC_Tax;

class WooDeliveryPrice {

	const PAYMENT_TITLE_SEPARATOR = ' + ';

	public function mapDelivery( $order = null ) {
		$options = [];
		foreach ( [ 'apm', 'courier' ] as $deliveryType ) {
			if ( ! $order ) {
				$parameters = $this->getDeliveryParameters( $deliveryType );
				if ( ! $parameters->available ) {
					Logger::response( json_encode( $parameters ) );
					continue;
				}
			} else {
				$price        = new Price();
				$price->gross = wc_format_decimal( $order->get_shipping_total(),
					2 );
				$price->net   = wc_format_decimal( $order->get_shipping_total() - $order->get_shipping_tax(),
					2 );
				$price->vat   = wc_format_decimal( $order->get_shipping_tax(),
					2 );
			}
			$delivery                   = new \Ilabs\Inpost_Pay\Lib\item\Delivery();
			$delivery->delivery_type    = strtoupper( $deliveryType );
			$delivery->delivery_date    = date( "Y-m-d\T12:00:00.000\Z",
				strtotime( " + 2 day" ) );
			$delivery->delivery_options = $this->mapDeliveryOptions( $deliveryType );
			$delivery->delivery_price   = ( $price ) ?? $this->mapDeliveryPrice( $parameters->net, $parameters->gross, $parameters->tax);
			$options[]                  = $delivery;
		}

		return $options;
	}

	public function getDeliveryParameters( $deliveryType ): \stdClass {
		$method   = esc_attr( get_option( 'izi_transport_method_' . $deliveryType ) );
		$response = [
			'available' => false,
			'net'       => 0,
			'tax'       => 0,
			'log'       => [],
		];

		if ( isset( \WC()->cart ) && \WC()->cart && ! \WC()->cart->is_empty() ) {
			foreach ( \WC()->cart->get_cart() as $cart_item ) {
				if ( ! $this->iziAvailableForProduct( $cart_item['data']->get_id(),
					$deliveryType ) ) {
					return (object) $response;
				}
			}
		}

		$shipping_packages = [];
		if ( isset( \WC()->cart ) && \WC()->cart && ! \WC()->cart->is_empty() ) {
			$shipping_packages = \WC()->cart->get_shipping_packages();
		}
		foreach ( array_keys( $shipping_packages ) as $key ) {
			$response['log'][] = 'shipping_package: ' . $key;
			if ( $shipping_for_package = \WC()->session->get( 'shipping_for_package_' . $key ) ) {
				$response['log'][] = 'shipping_package found : ' . $key;
				if ( isset( $shipping_for_package['rates'] ) ) {
					$response['log'][] = 'shipping_package rates present : ' . $key;
					foreach ( $shipping_for_package['rates'] as $rate_key => $rate ) {
						$response['log'][] = 'shipping_package looping through rates : ' . $rate->id;

						$rate_id          = $rate->get_id();
						$rate_method      = $rate->get_method_id();
						$rate_instance_id = $rate->get_instance_id();

						$rate_method_id  = $rate_method . ':' . $rate_instance_id;
						$shipping_method = WC_Shipping_Zones::get_shipping_method( $rate->get_instance_id() );
						$rate_label      = $rate->get_label();

						$is_free_shipping = $rate_method == 'free_shipping';
						if ( $rate_id == $method || $rate_method_id == $method || $is_free_shipping ) {


							if ( $shipping_method instanceof WC_Shipping_Method && $shipping_method->id === $rate_method ) {
								$shipping_method->calculate_shipping( $shipping_packages[ $key ] );
								$is_available          = $shipping_method->is_available( $shipping_packages[ $key ] );
								$response['available'] = $is_available;

								if ( isset( $shipping_method->rates[ $rate_method_id ] ) ) {
									/**
									 * @var WC_Shipping_Rate $found_rate
									 */
									$found_rate = $shipping_method->rates[ $rate_method_id ];
									$is_taxable = $shipping_method->is_taxable();

									if ( '0' === (string) get_option( "izi_transport_add_tax" ) ) {
										$option_add_vat_to_shipping_cost = false;
									} else {
										$option_add_vat_to_shipping_cost = true;
									}

									if ( $option_add_vat_to_shipping_cost ) {
										$taxes = $found_rate->get_taxes();
									} else {
										$taxes = WC_Tax::calc_tax( $found_rate->get_cost(),
											WC_Tax::get_shipping_tax_rates(),
											$is_taxable );
									}

									/*inpost_pay()
										->get_woocommerce_logger( 'izi_ppay_243' )
										->log_debug(
											sprintf( "[getDeliveryParameters] [taxes: %s] [taxes from rate: %s]",
												print_r( $taxes, true ),
												print_r( $found_rate->get_taxes(), true ),
											) );*/

									$response['net'] = $found_rate->get_cost();
									$response['tax'] = array_sum( $taxes );
									if ( false === $option_add_vat_to_shipping_cost ) {
										$response['gross'] = $response['net'];
										$response['net']   -= $response['tax'];
									} else {
										$response['gross'] = (float) $response['net'] + (float) $response['tax'];
									}

									if ( $is_available && $is_free_shipping ) {
										return (object) $response;
									}
								}
							}
						}
					}
				}
			}
		}

		return (object) $response;
	}


	public function mapDeliveryPrice( $net, $gross, $tax ): Lib\item\Price {
		$price = new Price();

		/*$gross2      = floor( $gross * pow( 10, 3 ) ) / pow( 10, 3 );
		$gross       = wc_format_decimal( $gross, 2 );
		$gross2      = wc_format_decimal( $gross2, 2 );
		$diff        = abs( ( floatval( $gross ) - floatval( $gross2 ) ) );
		if ( $diff > 0 && $diff <= 0.01 ) {
			$gross = $gross2;
		}

		$vat = $gross - $net;*/


		Logger::log( "NET SHIPPING: {$net}; GROSS: {$gross}, TAX: {$tax}" );


		$price->gross = wc_format_decimal( $gross, 2 );
		$price->net   = wc_format_decimal( $net, 2 );
		$price->vat   = wc_format_decimal( $tax, 2 );

		return $price;
	}

	public function mapDeliveryOptionPrice( $net ) {
		$price        = new Price();
		$gross        = $net * WooDeliveryPrice::getShippingTaxModifier();
		$vat          = $gross - $net;
		$price->gross = wc_format_decimal( $gross, 2 );
		$price->net   = wc_format_decimal( $net, 2 );
		$price->vat   = wc_format_decimal( $vat, 2 );

		return $price;
	}

	private function optionAvailability(
		string $option,
		string $deliveryType
	): bool {
		$dayOfWeek = date( 'N' );
		$hour      = date( 'H' );

		$dayFrom  = esc_attr( get_option( 'izi_transport_available_from_day_' . $option . '_' . $deliveryType ) );
		$dayTo    = esc_attr( get_option( 'izi_transport_available_to_day_' . $option . '_' . $deliveryType ) );
		$hourFrom = esc_attr( get_option( 'izi_transport_available_from_hour_' . $option . '_' . $deliveryType ) );
		$hourTo   = esc_attr( get_option( 'izi_transport_available_to_hour_' . $option . '_' . $deliveryType ) );

		if ( $dayOfWeek < $dayFrom ) {
			return false;
		}
		if ( $dayOfWeek == $dayFrom ) {
			if ( $hour < $hourFrom ) {
				return false;
			}
		}

		if ( $dayOfWeek > $dayTo ) {
			return false;
		}
		if ( $dayOfWeek == $dayTo ) {
			if ( $hour > $hourTo ) {
				return false;
			}
		}

		return true;
	}

	public function mapDeliveryOptions( $deliveryType ) {
		$data = [];

		$pwwPrice          = esc_attr( get_option( 'izi_transport_price_pww_' . $deliveryType ) );
		$pwwPriceAvailable = $this->optionAvailability( 'pww', $deliveryType );
		$codPrice          = esc_attr( get_option( 'izi_transport_price_cod_' . $deliveryType ) );
		$codPriceAvailable = $this->optionAvailability( 'cod', $deliveryType );;

		if ( $pwwPrice && $pwwPriceAvailable ) {
			$option                        = new \Ilabs\Inpost_Pay\Lib\item\DeliveryOption();
			$option->delivery_name         = "Paczka w Weekend";
			$option->delivery_code_value   = "PWW";
			$option->delivery_option_price = $this->mapDeliveryOptionPrice( (float) str_replace( ',',
				'.',
				$pwwPrice ) );
			$data[]                        = $option;
		}

		if ( $codPrice && $codPriceAvailable ) {
			$option                        = new \Ilabs\Inpost_Pay\Lib\item\DeliveryOption();
			$option->delivery_name         = "Pobranie";
			$option->delivery_code_value   = "COD";
			$option->delivery_option_price = $this->mapDeliveryOptionPrice( (float) str_replace( ',',
				'.',
				$codPrice ) );
			$data[]                        = $option;
		}

		return $data;
	}

	public static function getShippingTaxModifier() {
		return 1.23;
	}

	protected function iziAvailableForProduct( $id, $type ): bool {
		if ( get_option( 'izi_check_shipping_availability' ) == false ) {
			return true;
		}

		$configuredMethods = [
			explode( ':',
				esc_attr( get_option( 'izi_transport_method_' . $type ) ) )[0],
		];
		$allowedMethods    = get_post_meta( $id,
			'woo_inpost_shipping_methods_allowed',
			true );
		Logger::log( "METHODS FOR {$id}: " . print_r( $allowedMethods, true ) );
		if ( is_array( $allowedMethods ) ) {
			$found = false;
			foreach ( $allowedMethods as $method ) {
				$method = explode( ':', $method )[0];
				if ( in_array( $method, $configuredMethods ) ) {
					$found = true;
				}
			}

			return $found;
		}

		return true;
	}

	protected function get_shipping_method_title( $shipping_method_id = '' ) {
		$method_key_id = str_replace( ':', '_', $shipping_method_id );
		$option_name   = 'woocommerce_' . $method_key_id . '_settings';

		return ! empty( get_option( $option_name,
			true )['title'] ) ? get_option( $option_name, true )['title'] : '';
	}
}
