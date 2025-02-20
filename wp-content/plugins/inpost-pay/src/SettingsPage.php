<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\Lib\Authorization;
use Ilabs\Inpost_Pay\Lib\config\attribution\AttributionConfig;
use Ilabs\Inpost_Pay\Lib\config\attribution\AttributionOverridesConfig;
use Ilabs\Inpost_Pay\Lib\config\payment\PaymentMethodsOptions;
use Ilabs\Inpost_Pay\Lib\exception\AuthorizationException;
use Ilabs\Inpost_Pay\Lib\helpers\CacheHelper;

class SettingsPage {

	const OPT_KEY_PRODUCT_DESC_MAP = 'izi_product_desc_map';
	const OPT_DROPDOWN_ID_FULL_PRODUCT_DESC_MAP = 'full';
	const OPT_DROPDOWN_ID_SHORT_PRODUCT_DESC_MAP = 'short';
	const OPT_DROPDOWN_ID_DEFAULT_PRODUCT_DESC_MAP = self::OPT_DROPDOWN_ID_FULL_PRODUCT_DESC_MAP;
	private bool $check_authorization = false;
	private $plugin_name;

	public function __construct() {
		$this->plugin_name = 'inpost_pay';
	}

	public function register() {
		add_action( 'admin_menu', function () {
			add_menu_page( $this->plugin_name, 'InPost Pay', 'administrator', $this->plugin_name, array(
				$this,
				'displayPluginAdminDashboard'
			), 'dashicons-admin-settings', 26 );
		}, 9 );
		add_action( 'admin_init', function () {
			register_setting( 'inpost-izi', 'izi_transport_price_pww_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_day_pww_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_hour_pww_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_day_pww_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_hour_pww_apm' );

			register_setting( 'inpost-izi', 'izi_transport_price_cod_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_day_cod_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_hour_cod_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_day_cod_apm' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_hour_cod_apm' );

			register_setting( 'inpost-izi', 'izi_transport_price_pww_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_day_pww_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_hour_pww_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_day_pww_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_hour_pww_courier' );

			register_setting( 'inpost-izi', 'izi_transport_price_cod_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_day_cod_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_from_hour_cod_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_day_cod_courier' );
			register_setting( 'inpost-izi', 'izi_transport_available_to_hour_cod_courier' );

			register_setting( 'inpost-izi', 'izi_transport_method_apm' );
			register_setting( 'inpost-izi', 'izi_transport_method_courier' );
			register_setting( 'inpost-izi', 'izi_transport_add_tax', array(
				'type'    => 'bool',
				'default' => false
			) );

			register_setting( 'inpost-izi', 'izi_show_basket' );
			register_setting( 'inpost-izi', 'izi_place_basket' );
			register_setting( 'inpost-izi', 'izi_align_basket' );
			register_setting( 'inpost-izi', 'izi_background',
				array(
					'type'    => 'string',
					'default' => 'bright'
				) );
			register_setting( 'inpost-izi', 'izi_variant',
				array(
					'type'    => 'string',
					'default' => 'primary'
				) );
			register_setting( 'inpost-izi', 'izi_frame_style',
				array(
					'type'    => 'string',
					'default' => 'none'
				) );


			register_setting( 'inpost-izi', 'izi_show_order' );
			register_setting( 'inpost-izi', 'izi_place_order' );
			register_setting( 'inpost-izi', 'izi_align_order' );
			register_setting( 'inpost-izi', 'izi_button_order_max_width',
				array(
					'type'    => 'integer',
					'default' => '220'
				) );
			register_setting( 'inpost-izi', 'izi_button_order_min_height',
				array(
					'type'    => 'integer',
					'default' => '64'
				) );

			register_setting( 'inpost-izi', 'izi_show_checkout' );
			register_setting( 'inpost-izi', 'izi_place_checkout' );
			register_setting( 'inpost-izi', 'izi_align_checkout' );
			register_setting( 'inpost-izi', 'izi_button_checkout_max_width',
				array(
					'type'    => 'integer',
					'default' => '220'
				) );
			register_setting( 'inpost-izi', 'izi_button_checkout_min_height',
				array(
					'type'    => 'integer',
					'default' => '64'
				) );

			register_setting( 'inpost-izi', 'izi_show_login_page' );
			register_setting( 'inpost-izi', 'izi_place_login_page' );
			register_setting( 'inpost-izi', 'izi_align_login_page' );
			register_setting( 'inpost-izi', 'izi_button_login_page_max_width',
				array(
					'type'    => 'integer',
					'default' => '220'
				) );
			register_setting( 'inpost-izi', 'izi_button_login_page_min_height',
				array(
					'type'    => 'integer',
					'default' => '64'
				) );

			register_setting( 'inpost-izi', 'izi_show_minicart' );
			register_setting( 'inpost-izi', 'izi_place_minicart' );
			register_setting( 'inpost-izi', 'izi_align_minicart' );
			register_setting( 'inpost-izi', 'izi_button_minicart_max_width',
				array(
					'type'    => 'integer',
					'default' => '220'
				) );
			register_setting( 'inpost-izi', 'izi_button_minicart_min_height',
				array(
					'type'    => 'integer',
					'default' => '64'
				) );


			register_setting( 'inpost-izi', 'izi_show_list' );
			register_setting( 'inpost-izi', 'izi_place_list' );
			register_setting( 'inpost-izi', 'izi_align_list' );


			register_setting( 'inpost-izi', 'izi_button_cart_margin' );
			register_setting( 'inpost-izi', 'izi_button_cart_padding' );
			register_setting( 'inpost-izi', 'izi_button_cart_max_width',
				array(
					'type'    => 'integer',
					'default' => '220'
				) );
			register_setting( 'inpost-izi', 'izi_button_cart_min_height',
				array(
					'type'    => 'integer',
					'default' => '64'
				) );
			register_setting( 'inpost-izi', 'izi_button_details_margin' );
			register_setting( 'inpost-izi', 'izi_button_details_padding' );
			register_setting( 'inpost-izi', 'izi_button_details_max_width',
				array(
					'type'    => 'integer',
					'default' => '220'
				) );
			register_setting( 'inpost-izi', 'izi_button_details_min_height',
				array(
					'type'    => 'integer',
					'default' => '64'
				) );

			register_setting( 'inpost-izi', 'izi_show_details' );
			register_setting( 'inpost-izi', 'izi_place_details' );
			register_setting( 'inpost-izi', 'izi_align_details' );

			register_setting( 'inpost-izi', 'izi_client_id' );
			register_setting( 'inpost-izi', 'izi_environment' );
			register_setting( 'inpost-izi', 'izi_client_secret' );
			register_setting( 'inpost-izi', 'izi_merchant_payment' );
			register_setting( 'inpost-izi', 'izi_hide_functionality' );

			register_setting( 'inpost-izi', 'izi_consents' );

			register_setting( 'inpost-izi', 'izi_event_AUTHORIZED' );
			register_setting( 'inpost-izi', 'izi_status_map' );

			register_setting( 'inpost-izi', self::OPT_KEY_PRODUCT_DESC_MAP );
			register_setting( 'inpost-izi', 'izi_related_count' );

			register_setting( 'inpost-izi', 'izi_pos_id' );

			register_setting( 'inpost-izi', 'izi_payment_aion' );
			register_setting( 'inpost-izi', 'izi_payment_inpost' );

			register_setting( 'inpost-izi', 'izi_debug', array(
				'type'    => 'bool',
				'default' => false
			) );

			register_setting( 'inpost-izi', 'izi_sse_sleep_time', array(
				'type'              => 'number',
				'default'           => 1.5,
				'sanitize_callback' => [ $this, 'validate_sse_sleep_time' ],
			) );

			register_setting( 'inpost-izi', 'izi_check_shipping_availability', array(
				'type'    => 'bool',
				'default' => false
			) );

			register_setting( 'inpost-izi', 'izi_is_authorized', array(
				'type'    => 'bool',
				'default' => false
			) );

			register_setting( 'inpost-izi', 'izi_omnibus_show_on_listing', array(
				'type'    => 'bool',
				'default' => false
			) );

			register_setting( 'inpost-izi', 'izi_omnibus_show_on_none_discount_products', array(
				'type'    => 'bool',
				'default' => false
			) );

			( new PaymentMethodsOptions() )->register();
			( new AttributionConfig() )->register();
			( new AttributionOverridesConfig() )->register();
		} );

		add_filter( "pre_update_option_izi_client_secret", function ( $value, $old_value ) {
			if ( $old_value && ( ! str_replace( '*', '', $value ) ) ) {
				return $old_value;
			}

			return $value;
		}, 10, 2 );

		$sleepTime = get_option( 'izi_sse_sleep_time' );
		if ( $sleepTime < 0.1 ) {
			update_option( 'izi_sse_sleep_time', 1.5 );
		}


	}

	public function check_authorization(): void {
		if (!$this->check_authorization) {
			$this->check_authorization = true;
			$authorization = new Authorization();
			try {
				$authorization->getToken( true );
				update_option('izi_is_authorized', true);
				CacheHelper::flushCache();
			} catch ( AuthorizationException $ex ) {
				add_settings_error(
					'izi_messages',
					'izi_message',
					__( 'Wrong credentials', 'inpost-pay' )
				);
			}
		}
	}

	public function validate_sse_sleep_time( $value ): string {
		$oldValue = get_option( 'izi_sse_sleep_time' );
		if ( $value < 0.1 ) {
			add_settings_error(
				'izi_messages',
				'izi_message',
				__( 'SEE delay time response must be greater than 0.1', 'inpost-pay' )
			);
			$value = $oldValue;
		}
		if ( $value > 3 ) {
			add_settings_error(
				'izi_messages',
				'izi_message',
				__( 'SEE delay time response must be less than 3', 'inpost-pay' )
			);
			$value = $oldValue;
		}

		return number_format( $value, 1, '.', '' );
	}

	private function allShippingZones() {
		$data_store = \WC_Data_Store::load( 'shipping-zone' );
		$raw_zones  = $data_store->get_zones();
		foreach ( $raw_zones as $raw_zone ) {
			$zones[] = new \WC_Shipping_Zone( $raw_zone );
		}
		$zones[] = new \WC_Shipping_Zone( 0 ); // ADD ZONE "0" MANUALLY

		return $zones;
	}

	private function getAllAvailableShipping() {
		$available = [];
		foreach ( $this->allShippingZones() as $zone ) {
			$zone_shipping_methods = $zone->get_shipping_methods();
			foreach ( $zone_shipping_methods as $index => $method ) {
				$available[ $method->get_rate_id() ] = $method->get_title();
			}
		}

		return $available;
	}

	public function displayPluginAdminDashboard() {
		$consentRequirement = [
			'OPTIONAL'        => __( 'Optional', 'inpost-pay' ),
			'REQUIRED_ONCE'   => __( 'Required once', 'inpost-pay' ),
			'REQUIRED_ALWAYS' => __( 'Required always', 'inpost-pay' ),
		];

		$daysOfWeek = [
			1 => __( 'Monday', 'inpost-pay' ),
			2 => __( 'Tuesday', 'inpost-pay' ),
			3 => __( 'Wednesday', 'inpost-pay' ),
			4 => __( 'Thursday', 'inpost-pay' ),
			5 => __( 'Friday', 'inpost-pay' ),
			6 => __( 'Saturday', 'inpost-pay' ),
			7 => __( 'Sunday', 'inpost-pay' ),
		];

		$hoursOfDay = range( 0, 23 );

		$availableAligns = [
			'left'   => __( 'To the left', 'inpost-pay' ),
			'center' => __( 'To the center', 'inpost-pay' ),
			'right'  => __( 'To the right', 'inpost-pay' ),
		];

		$availableBackgrounds = [
			'bright' => __( 'Bright', 'inpost-pay' ),
			'dark'   => __( 'Dark', 'inpost-pay' ),
		];

		$availableVariants = [
			'primary'   => __( 'Yellow', 'inpost-pay' ),
			'secondary' => __( 'Black', 'inpost-pay' ),
		];

		$availableFrameStyle = [
			'none'    => __( 'No round', 'inpost-pay' ),
			'round'   => __( 'Big round', 'inpost-pay' ),
			'rounded' => __( 'Small round', 'inpost-pay' ),
		];

		$availableShippingMethods = $this->getAllAvailableShipping();
		$paymentOptions           = get_option( 'izi_merchant_payment' );
		$button_cart_margin       = get_option( 'izi_button_cart_margin' );
		$button_cart_padding      = get_option( 'izi_button_cart_padding' );
		$button_details_margin    = get_option( 'izi_button_details_margin' );
		$button_details_padding   = get_option( 'izi_button_details_padding' );
		$checked                  = function ( $name ) use ( $paymentOptions ) {
			if ( ! is_array( $paymentOptions ) ) {
				return '';
			}
			if ( in_array( $name, $paymentOptions ) ) {
				return 'checked';
			}

			return '';
		};
		require_once __DIR__ . '/views/admin.php';
	}

	public static function statusDropdown( $status ) {
		$value = esc_attr( get_option( 'izi_event_' . $status ) );
		echo "<select name='izi_event_{$status}'>";
		echo "<option value=''>" . __( 'Select', 'inpost-pay' ) . "</option>";
		foreach ( StatusTranslator::ayastmAvailableStatusses() as $system => $availableLabel ) {
			$selected = $value == $system ? 'selected' : '';
			echo "<option {$selected} value='{$system}'>{$availableLabel}</option>";
		}
		echo "</select>";
	}

	public static function productDescMapDropdown() {
		$optId = self::OPT_KEY_PRODUCT_DESC_MAP;
		$value = esc_attr( get_option( $optId,
			self::OPT_DROPDOWN_ID_DEFAULT_PRODUCT_DESC_MAP ) );

		echo "<select name='{$optId}'>";
		foreach (
			[
				self::OPT_DROPDOWN_ID_FULL_PRODUCT_DESC_MAP  => __( 'Full description',
					'inpost-pay' ),
				self::OPT_DROPDOWN_ID_SHORT_PRODUCT_DESC_MAP => __( 'Short description',
					'inpost-pay' ),
			] as $key => $label
		) {
			$selected = $value === $key ? 'selected' : '';
			echo "<option {$selected} value='{$key}'>{$label}</option>";
		}
		echo "</select>";
	}

	public static function statusMap() {
		$value = get_option( 'izi_status_map' );
		echo '<table><tbody>';
		foreach ( StatusTranslator::ayastmAvailableStatusses() as $system => $availableLabel ) {
			$label = ( ! empty( $value[ $system ] ) ) ? esc_attr( $value[ $system ] ) : $availableLabel;
			echo "<tr><td>{$availableLabel}</td><td><input type='text' name='izi_status_map[{$system}]' value='{$label}'></td></tr>";
		}
		echo "</tbody></table>";
	}
}
