<?php

namespace Ilabs\Inpost_Pay\Lib\Coupons;

use Ilabs\Inpost_Pay\Lib\helpers\DateHelper;
use Ilabs\Inpost_Pay\Logger;
use WC_Coupon;
use WP_Query;

class PromotionsAvailable {
	public function get_coupons(): array {
		$query_args = [
			'posts_per_page' => 500,
			'orderby'        => 'date',
			'order'          => "DESC",
			'no_found_rows'  => true,
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
		];


		$coupons           = new WP_Query( $query_args );
		$coupon_ids        = [];
		$available_coupons = [];

		if ( $coupons->have_posts() ) {
			while ( $coupons->have_posts() ) {
				$coupons->the_post();

				$coupon_ids[] = get_the_ID();
			}

			wp_reset_postdata();
		}

		if ( ! empty( $coupon_ids ) ) {


			$applied_coupons = WC()->cart->get_applied_coupons() ?: [];
			$cart_subtotal   = WC()->cart->get_subtotal();
			$cart_item       = WC()->cart->get_cart();
			$now             = current_time( 'timestamp' );
			$products        = [];

			if ( ! empty( $cart_item ) ) {
				foreach ( $cart_item as $item ) {
					$product_id = $item['variation_id'] ?: $item['product_id'];
					$product    = wc_get_product( $product_id );
					$products[] = $product;
				}
			}

			foreach ( $coupon_ids as $coupon_id ) {
				$coupon         = new WC_Coupon( $coupon_id );
				$date_expire    = null !== $coupon->get_date_expires() ? strtotime( $coupon->get_date_expires( 'edit' )->date( 'Y-m-d' ) ) : '';
				$coupon_message = '';

				// Skip coupon if it has expired.
				if ( '' !== $date_expire && $now > $date_expire ) {
					Logger::log( 'Coupon expired - ' . $coupon->get_code() );
					continue;
				}

				// Skip coupon if it applied in cart.
				if ( in_array( $coupon->get_code(), $applied_coupons, true ) ) {
					Logger::log( 'Coupon is already in cart - ' . $coupon->get_code() );
					continue;
				}

				// Check coupons have limitation to user.
				$restrictions = $coupon->get_email_restrictions();

				//Skip coupon if it has limitation to user. Because we don't have user email address to check
				if ( is_array( $restrictions ) && 0 < count( $restrictions ) ) {
					Logger::log( 'Coupon has limitation to user - ' . $coupon->get_code() );
					continue;
				}

				// Skip coupon if products in cart not fit with usage restriction.
				if ( ! empty( $products ) ) {
					$continue = false;


					if ( $coupon->get_exclude_sale_items() ) {
						foreach ( $products as $product ) {
							if ( $product->is_on_sale() ) {
								$continue = true;
								break;
							}
						}

						if ( $continue ) {
							Logger::log( 'Coupon is exclude sale items - ' . $coupon->get_code() );
							continue;
						}
					}

					if ( count( $coupon->get_excluded_product_ids() ) > 0 ) {
						foreach ( $products as $product ) {
							if ( in_array( $product->get_id(), $coupon->get_excluded_product_ids(), true ) || in_array( $product->get_parent_id(), $coupon->get_excluded_product_ids(), true ) ) {
								$continue = true;
								break;
							}
						}

						if ( $continue ) {
							Logger::log( 'Coupon is exclude by product - ' . $coupon->get_code() );
							continue;
						}
					}

					if ( count( $coupon->get_excluded_product_categories() ) > 0 ) {
						foreach ( $products as $product ) {
							$product_cats = wc_get_product_cat_ids( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() );

							if ( ! count( array_intersect( $product_cats, $coupon->get_excluded_product_categories() ) ) ) {
								$continue = true;
								break;
							}
						}

						if ( $continue ) {
							Logger::log( 'Coupon is exclude by category - ' . $coupon->get_code() );
							continue;
						}
					}

					if ( count( $coupon->get_product_ids() ) > 0 ) {
						$product_ids = [];
						foreach ( $products as $product ) {
							$product_ids[] = $product->get_id();
							if ( $product->get_parent_id() > 0 ) {
								$product_ids[] = $product->get_parent_id();
							}
						}

						// Specific products get the discount.
						if ( ! count( array_intersect( $product_ids, $coupon->get_product_ids() ) ) ) {
							$continue = true;
						}

						if ( $continue ) {
							Logger::log( 'Coupon is not valid for cart - ' . $coupon->get_code() );
							continue;
						}
					}

					if ( $coupon->is_type( wc_get_product_coupon_types() ) ) {
						foreach ( $products as $product ) {
							$continue = $coupon->is_valid_for_product( $product );

							if ( ! $continue ) {
								break;
							}
						}

						if ( ! $continue ) {
							Logger::log( 'Coupon is not valid for product (Woo Check) - ' . $coupon->get_code() );
							continue;
						}
					}

					foreach ( $products as $product ) {

						$continue = apply_filters( 'woocommerce_coupon_is_valid_for_product', true, $product, $this, [] );

						if ( ! $continue ) {
							break;
						}
					}

					if ( ! $continue ) {
						Logger::log( 'Coupon is not valid for product (Woo Filter Check) - ' . $coupon->get_code() );
						continue;
					}


				}


				$minimum_amount = $coupon->get_minimum_amount();
				$maximum_amount = $coupon->get_maximum_amount();

				// Disable coupon if cart subtotal spent lest than minimum amount required.
				if ( $minimum_amount > 0 && apply_filters( 'woocommerce_coupon_validate_minimum_amount', $minimum_amount > $cart_subtotal, $coupon, $cart_subtotal ) ) {
					$coupon_message = sprintf(
						esc_html__( 'The minimum spend for this coupon is %s.', 'inpost-pay' ),
						number_format( $minimum_amount, 2, '.', '' )
					);
				}

				// Disable coupon if cart subtotal spent more than maximum amount required.
				if ( $maximum_amount != '' && apply_filters( 'woocommerce_coupon_validate_maximum_amount', $maximum_amount < $cart_subtotal, $coupon ) ) {
					Logger::log( 'Coupon is not valid for cart subtotal - ' . $coupon->get_code() );
					continue;
				}

				if ( $coupon_message === '' ) {
					if ( strip_tags( $coupon->get_description() ) === '' ) {
						Logger::log( 'Coupon skip no description - ' . $coupon->get_code() );
						continue;
					}

					$coupon_message = strip_tags( $coupon->get_description() );
				}

				$url = '';

				$promotions_available = new \Ilabs\Inpost_Pay\Lib\item\PromotionsAvailable();

				$promotions_available->set_promo_code_value( $coupon->get_code() );
				$promotions_available->set_description( $coupon_message );
				$promotions_available->set_type( $coupon->get_discount_type() );
				$promotions_available->set_start_date( $coupon->get_date_created() ? $coupon->get_date_created()->date( DateHelper::DATE_API_FORMAT ) : '' );
				$promotions_available->set_end_date( $coupon->get_date_expires() ? $coupon->get_date_expires()->date( DateHelper::DATE_API_FORMAT ) : '' );


				$url = $coupon->get_meta( Coupon::META_PROMOTION_URL );


				if ( $url === '' ) {
					$url = get_permalink( wc_get_page_id( 'shop' ) );
				}

				$promotions_available->details->link = $url;


				$available_coupons[] = $promotions_available;
			}
		}

		return $available_coupons;
	}
}
