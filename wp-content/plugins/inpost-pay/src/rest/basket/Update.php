<?php

namespace Ilabs\Inpost_Pay\rest\basket;

use Exception;
use Ilabs\Inpost_Pay\Integration\Basket\BundledItem;
use Ilabs\Inpost_Pay\Integration\Basket\BundledItemFactory;
use Ilabs\Inpost_Pay\Integration\Basket\CartItemFilter;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\objects\CartProductId;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\WooCommerceBasket;
use Ilabs\Inpost_Pay\WooCommerceBasketCache;
use WC_Product_Variable;
use WP_REST_Response;

class Update extends
	Base {

	const EVENT_TYPE_PROMO_CODES = 'PROMO_CODES';

	const EVENT_TYPE_PRODUCTS_QUANTITY = 'PRODUCTS_QUANTITY';

	const EVENT_TYPE_RELATED_PRODUCTS = 'RELATED_PRODUCTS';


	protected $hasCoupons = false;
	protected $couponError = false;

	public function __construct() {
		$this->restricted = true;
	}

	protected function describe() {
		$this->post['/inpost/v1/izi/basket/(?P<id>[a-zA-Z0-9-]+)/event'] = function (
			$request
		) {
			define( 'DOING_AJAX', true );
			$this->checkSignature( $request );

			$id   = $request->get_param( 'id' );
			$data = $request->get_body();
			InPostIzi::blockPut();
			$date = date( "Y-m-d H:i:s" );
			Logger::basketEvent( $data,
				"Event dla koszyka {$id} z {$date} {$_SERVER['REQUEST_URI']}" );
			$data = json_decode( $data );

			WooCommerceBasketCache::restore( $id, false );

			switch ( $data->event_type ) {
				case self::EVENT_TYPE_PRODUCTS_QUANTITY:
					foreach ( $data->quantity_event_data as $eventData ) {
						$quantity = $eventData->quantity->quantity;
						$this->updateQuantity( $eventData->product_id,
							$quantity );
					}
					break;
				case self::EVENT_TYPE_PROMO_CODES:
					$appliedCodes     = [];
					$this->hasCoupons = true;
					foreach ( $data->promo_codes_event_data as $eventData ) {
						$appliedCodes[] = $this->applyCode( $eventData->promo_code_value );
					}

					foreach ( \WC()->cart->get_applied_coupons() as $code ) {
						if ( ! in_array( $code, $appliedCodes ) ) {
							\WC()->cart->remove_coupon( $code );
						}
					}
					WooCommerceBasket::$couponError = $this->couponError;
					break;
				case self::EVENT_TYPE_RELATED_PRODUCTS:
					if ( isset( $data->related_products_event_data[0], $data->related_products_event_data[0]->product_id ) ) {
						foreach ( $data->related_products_event_data as $data ) {
							if ( $this->checkAvailability( $data->product_id ) ) {
								\WC()->cart->add_to_cart( $data->product_id );
							}
						}
					} else {
						Logger::log( 'TO MAM: ' . print_r( $data, true ) );
					}
					break;
			}

			$cart                = WC()->cart;
			$cart->cart_contents = apply_filters( 'woocommerce_cart_contents_changed', $cart->cart_contents );
			$cart->calculate_shipping();
			$cart->calculate_fees();
			$cart->calculate_totals();

			add_action('shutdown', function () use ($id) {
				$basket = WooCommerceBasket::getBasket( false )->encode();
				CartSession::setBasketCacheById( $id, $basket );
				CartSession::setBasketCachedById( $id );
				CartSession::setBasketCouponsById( $id, '1' );
				Logger::rawData( $basket, 'BASKET FROM UPDATE' );
				die( mb_convert_encoding( $basket, 'UTF-8' ) );
			}, PHP_INT_MAX - 1);

			return rest_ensure_response(new WP_REST_Response(
				null,
				200,
				[]
			));
		};
	}

	protected function updateQuantity( $productId, $quantity ) {
		$items           = \WC()->cart->get_cart();
		$cart_product_id = new CartProductId( $productId );

		if ( $cart_product_id->hasKey() ) {
			$cart_item              = $items[$cart_product_id->getKey()];
			$bundleItem = BundledItemFactory::create($cart_item, \WC()->cart );

			if ( $quantity === 0 ) {

				if ($bundleItem instanceof BundledItem){
					$bundleItem->removeParentWithBundledItems();

					return;
				}

				/*if ( ! $cartItemFilter->canAddCartItem( $cart_item ) ) {
					$wooco_parent_id              = $cart_item['wooco_parent_id'];
					foreach ( $items as $cart_item_key_2 => $item_2 ) {
						if ( ( $item_2['product_id'] ) == $wooco_parent_id ) {
							\WC()->cart->remove_cart_item( $cart_item_key_2 );

							return;
						}
					}
				}*/

				\WC()->cart->remove_cart_item( $cart_product_id->getKey() );
			} else {

				if ($bundleItem instanceof BundledItem){
					return;
				}

				\WC()->cart->set_quantity( $cart_product_id->getKey(), $quantity );
			}

			return;
		}

		foreach ( $items as $cart_item_key => $item ) {
			$bundleItem = BundledItemFactory::create($item, \WC()->cart );
			if ($bundleItem instanceof BundledItem){
				$bundleItem->removeParentWithBundledItems();

				return;
			}
			/*if ( ! $cartItemFilter->canAddCartItem( $item ) ) {
				$wooco_parent_id              = $item['wooco_parent_id'];
				foreach ( $items as $cart_item_key_2 => $item_2 ) {
					if ( ( $item_2['product_id'] ) == $wooco_parent_id ) {
						\WC()->cart->remove_cart_item( $cart_item_key_2 );

						return;
					}
				}
			}*/

			if ( isset( $item['product_id'] ) ) {
				if ( ( $item['product_id'] ) == $cart_product_id->getId() || $item['variation_id'] == $cart_product_id->getId() ) {
					if ( $quantity === 0 ) {
						\WC()->cart->remove_cart_item( $cart_item_key );
					} else {
						\WC()->cart->set_quantity( $cart_item_key, $quantity );
					}

				}
			}
		}

	}

	protected function applyCode( $code ) {
		if ( in_array( $code, \WC()->cart->get_applied_coupons() ) ) {
			return;
		}

		$couponObject = new \WC_Coupon( $code );



		if ( ! $couponObject ) {
			$this->couponError = true;

			return;
		}
		$code = $couponObject->get_code();

		WooCommerceBasket::$hasCoupons = $this->hasCoupons;
		if ( ! \WC()->cart->has_discount( $code ) ) {
			if ( ! \WC()->cart->apply_coupon( $code ) ) {
				$this->couponError = true;
			}
			if ( ! \WC()->cart->has_discount( $code ) ) {
				$this->couponError = true;
			} else {
				wc_clear_notices();
				wc_add_notice( __( 'Coupon added using InPost Pay',
					'woocommerce' ), 'success' );
			}



		} else {
			$this->couponError = true;
		}

		return $code;
	}

	protected function checkAvailability( $product_id ): bool {
		$product = wc_get_product( $product_id );
		if ( ! empty( $product ) && $product->is_purchasable() && $product->is_in_stock() && $product->is_visible() ) {
			return true;
		}

		return false;
	}
}
