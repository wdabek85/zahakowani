<?php

namespace Ilabs\Inpost_Pay\Lib\Coupons;

use Automattic\WooCommerce\Utilities\NumberUtil;
use Exception;
use Ilabs\Inpost_Pay\InpostPay;
use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\rest\RestRequest;
use JsonException;
use WC_Admin_Meta_Boxes;
use WC_Coupon;
use WC_Discounts;

class Coupon {

	public const COUPON_TYPE = 'inpost_pay_discount';

	public const META_PROMOTION_URL = 'inpost_pay_promotion_url';

	public function hooks(): void {
		add_filter( 'woocommerce_coupon_discount_types', array( $this, 'inpost_pay_custom_discount_type' ), 10, 1 );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'inpost_pay_save_coupon_meta' ), 10, 2 );
		add_action( 'woocommerce_coupon_options', array(
			$this,
			'inpost_pay_coupon_options'
		), 10, 2 );

		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'inpost_pay_validate_custom_coupon' ), 10, 3 );

		add_filter( 'woocommerce_coupon_get_discount_amount', array(
			$this,
			'inpost_pay_apply_custom_coupon_discount'
		), 10, 5 );

//		add_filter( 'woocommerce_coupon_discount_amount_html', array( $this,'inpost_pay_coupon_discount_amount_html_filter' ), 10, 2 );

		add_filter( 'woocommerce_product_coupon_types', array(
			$this,
			'inpost_pay_woocommerce_product_coupon_types'
		), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_coupon_scripts' ), 75 );


	}

	public function inpost_pay_custom_discount_type( $discount_types ) {
		$discount_types[ self::COUPON_TYPE ] = __( 'InpostPay', 'inpost-pay' );

		return $discount_types;
	}


	public function inpost_pay_save_coupon_meta( $post_id, $coupon ): void {
		if ( isset( $_POST[ self::META_PROMOTION_URL ] ) ) {
			if ( ! $this->validate_meta_field( $_POST[ self::META_PROMOTION_URL ] ) ) {
				WC_Admin_Meta_Boxes::add_error( __( 'Enter valid url address', 'inpost-pay' ) );

				return;
			}
			$url = sanitize_text_field( $_POST[ self::META_PROMOTION_URL ] );
			update_post_meta( $post_id, self::META_PROMOTION_URL, $url );
		}
	}

	public function inpost_pay_coupon_options( $coupon_id, $coupon ): void {

		echo '<div id="inpost_pay_promotion_url" class="panel woocommerce_options_panel" style="margin-top:20px; display: block;">';

		$meta = get_post_meta( $coupon_id, self::META_PROMOTION_URL, true );

		woocommerce_wp_text_input( [
			'id'        => self::META_PROMOTION_URL,
			'label'     => __( 'Promotion URL', 'inpost-pay' ),
			'value'     => $meta,
			'style'     => ( !$this->validate_meta_field( $meta ) ) ? 'border: 1px solid red' : '',
			'data_type' => 'url'
		] );

		echo '</div>';
	}

	private function validate_meta_field( $value ): bool {
		if ( ! empty( $value ) &&
		     ! preg_match( '/^(https?:\/\/[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})([\/?].*)?$/', $value ) ) {
			return false;
		}

		return true;

	}

	/**
	 * @throws JsonException
	 */
	public function inpost_pay_validate_custom_coupon( $is_valid, WC_Coupon $coupon, WC_Discounts $discount ) {
		if ( $coupon->get_discount_type() === self::COUPON_TYPE ) {

			if ( RestRequest::isRequested() ) {
				return true;
			}

			$data = InPostIzi::getCartSessionClass()::getCartConfirmation( BasketIdentification::get() );
			if ( is_string( $data ) && strlen( $data ) > 10 ) {
				$data = json_decode( $data, false, 512, JSON_THROW_ON_ERROR );

				if ( $data->status !== 'SUCCESS' ) {
					return false;
				}
			} else {
				return false;
			}
		}

		return $is_valid;
	}

	public function inpost_pay_coupon_discount_amount_html_filter( $discount_amount_html, $coupon ) {

		if ( $coupon->get_discount_type() === self::COUPON_TYPE ) {
			if ( $coupon->get_free_shipping() ) {
				return __( 'Free shipping coupon', 'woocommerce' );
			}

			$inpost_coupon_amount = (float) $coupon->get_amount();
			if ( $inpost_coupon_amount > 0 ) {
				return '-' . wc_price( $inpost_coupon_amount );
			}
		}

		return $discount_amount_html;
	}

	public function inpost_pay_apply_custom_coupon_discount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( $coupon->get_discount_type() === self::COUPON_TYPE ) {
			$discount = (float) $coupon->get_amount() * ( $discounting_amount / 100 );

			return NumberUtil::round( min( $discount, $discounting_amount ), wc_get_rounding_precision() );
		}

		return $discount;
	}

	public function inpost_pay_woocommerce_product_coupon_types( $types ): array {
		$types[] = self::COUPON_TYPE;

		return $types;
	}

	function enqueue_admin_coupon_scripts() {

		$current_screen = get_current_screen();

		// only on edit coupon page
		if ( $current_screen instanceof \WP_Screen && 'shop_coupon' === $current_screen->id ) {
			$inpostPay = InpostPay::getInstance();

			wp_enqueue_script( 'inpostpay-coupons', $inpostPay->getJsAssetPath() . 'admin-coupon-script.js', [ 'jquery' ] );

		}
	}

}
