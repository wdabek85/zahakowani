<?php

namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Lib\Remote;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class BasketChange extends Base {
	public static bool $BLOCK_ACTION_SET = false;

	public static bool $HOOK_IS_START = false;

	public function attachHook() {
		$hook = function () {
			if (self::$HOOK_IS_START) {
				return;
			}

			$end_hook = function () {
				$izi          = WooCommerceInPostIzi::getInstance();
				Remote::$done = false;
				$izi->basketPut();
				$count = \WC()->cart->get_cart_contents_count();
				Logger::log('SET ACTION: update-count:' . $count);
				CartSession::setActionById( BasketIdentification::get(), 'update-count:' . $count );
				self::$BLOCK_ACTION_SET = true;
			};

			self::$HOOK_IS_START = true;
			Logger::log( 'Performing update because of: ' . current_action() );


			if ( self::$BLOCK_ACTION_SET === false ) {
				add_action( 'shutdown', $end_hook);
			}

		};

		add_action( 'woocommerce_update_cart_action_cart_updated', $hook, 9999 );
		add_action( 'woocommerce_add_to_cart', $hook, 9999 );
		add_action( 'woocommerce_cart_item_removed', $hook, 9999 );
//		add_action( 'woocommerce_cart_item_set_quantity', $hook );
		add_action( 'woocommerce_cart_item_restored', $hook, 9999 );

		$coupon_hook = function () {
			if (self::$HOOK_IS_START) {
				return;
			}
			self::$HOOK_IS_START = true;
			Logger::log( 'Performing update because of: ' . current_action() );
			$izi          = WooCommerceInPostIzi::getInstance();
			Remote::$done = false;
			$izi->basketPut();
		};

		add_action( 'woocommerce_applied_coupon', $coupon_hook );
		add_action( 'woocommerce_removed_coupon', $coupon_hook );

	}
}
