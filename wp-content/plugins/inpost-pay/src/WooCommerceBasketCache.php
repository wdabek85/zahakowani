<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\hooks\BasketChange;
use Ilabs\Inpost_Pay\Integration\Basket\CartItemFilter;
use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\rest\RestRequest;
use WC_Cart;

class WooCommerceBasketCache {

	public static function store( $cart_id = null ): string {
		self::restore( $cart_id );

		$cart = WC()->cart;

		$storeCart['cart_contents']   = $cart->cart_contents;
		$storeCart['applied_coupons'] = $cart->applied_coupons;
		$storeCart['fees_api']        = $cart->fees_api;

		return serialize( $storeCart );
	}


	public static function restore( $cartId, $calculate_totals = true ) {
		$logger = inpost_pay()->get_woocommerce_logger( 'izi_1121' );

		if ( RestRequest::isRequested() === false ) {
			return;
		}


		$logger->log_debug( '[restore]');

		BasketChange::$BLOCK_ACTION_SET = true;
		BasketChange::$HOOK_IS_START    = true;
		CartSession::setSessionByCartId( $cartId );
		CartSession::initiateWCCart();
		BasketIdentification::set( $cartId );

		if ( ! \WC()->cart->is_empty() ) {
			return;
		}

		$contents = unserialize( CartSession::getBasketCachedById( $cartId ) );
		if ( ! is_array( $contents['cart_contents'] ) ) {
			return;
		}
		if ( \WC()->cart->is_empty() ) {
			foreach ( $contents['cart_contents'] as $key => $item ) {
				if ( ! empty( $item['tmcartepo'] ) ) {
					$contents['cart_contents'][ $key ]['tc_recalculate'] = true;
				}
			}
			\WC()->cart->cart_contents = $contents['cart_contents'];
			foreach ( $contents['applied_coupons'] as $coupon ) {
				\WC()->cart->apply_coupon( $coupon );
			}
		}

	}

}
