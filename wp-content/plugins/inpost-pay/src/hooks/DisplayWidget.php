<?php

namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Lib\BindingProvider;
use Ilabs\Inpost_Pay\Lib\view\DisplayPlaceHolder;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;

class DisplayWidget extends Base {
	public function attachHook() {
		if ( esc_attr( get_option( 'izi_show_order' ) ) ) {
			add_action( esc_attr( get_option( 'izi_place_order' ) ), [ $this, 'displayOrderPlaceholder' ] );
		}
		if ( esc_attr( get_option( 'izi_show_checkout' ) ) ) {
			add_action( esc_attr( get_option( 'izi_place_checkout' ) ), [ $this, 'displayCheckoutPlaceholder' ] );
		}
		if ( esc_attr( get_option( 'izi_show_login_page' ) ) ) {
			add_action( esc_attr( get_option( 'izi_place_login_page' ) ), [
				$this,
				'displayLoginPagePlaceholder'
			], 20 );
		}
		if ( esc_attr( get_option( 'izi_show_minicart' ) ) ) {
			add_action( esc_attr( get_option( 'izi_place_minicart' ) ), [ $this, 'displayMinicartPlaceholder' ] );
		}
		if ( esc_attr( get_option( 'izi_show_basket' ) ) ) {
			add_action( esc_attr( get_option( 'izi_place_basket' ) ), [ $this, 'displayCartPlaceholder' ] );
		}

		if ( esc_attr( get_option( 'izi_show_list' ) ) ) {
			add_action( 'woocommerce_after_shop_loop_item', function ( $data ) {
				global $post;

				if ( ! $this->iziAvailableForProduct( $post->ID ) ) {
					return;
				}

				$this->display(
					$post->ID,
					'',
					esc_attr( get_option( 'izi_background' ) ) == 'dark',
					esc_attr( get_option( 'izi_variant' ) ) == 'primary',
					false,
					esc_attr( get_option( 'izi_align_list' ) ),
					InPostIzi::BINDING_PLACE_PRODUCT_CARD,
					esc_attr( get_option( 'izi_frame_style' ) ),
					esc_attr( get_option( 'izi_button_details_max_width' ) ),
					esc_attr( get_option( 'izi_button_details_min_height' ) ),
				);
			}, 10 );
		}

		if ( esc_attr( get_option( 'izi_show_details' ) ) ) {
			add_action( esc_attr( get_option( 'izi_place_details', 'woocommerce_after_add_to_cart_button' ) ), [
				$this,
				'displayProductPlaceholder'
			] );
		}
	}

	public function displayProductPlaceholder( $id = null, $styles = '' ) {
		DisplayPlaceHolder::displayProductPlaceholder();
	}

	public function displayCartPlaceholder( $id = null, $styles = '' ) {
		DisplayPlaceHolder::displayCartPlaceholder();
	}

	public function displayOrderPlaceholder( $id = null, $styles = '' ) {
		DisplayPlaceHolder::displayOrderPlaceHolder();
	}

	public function displayCheckoutPlaceholder( $id = null, $styles = '' ) {
		DisplayPlaceHolder::displayCheckoutPlaceholder();
	}

	public function displayLoginPagePlaceholder( $id = null, $styles = '' ) {
		DisplayPlaceHolder::displayLoginPagePlaceholder();
	}

	public function displayMinicartPlaceholder( $id = null, $styles = '' ) {
		DisplayPlaceHolder::displayMinicartPlaceholder();
	}

	public function displayProduct( $id = null, $styles = '' ) {
		if ( empty( $id ) && is_product() ) {
			global $post;
			$id = $post->ID;
		}

		$button_details_margin = get_option( 'izi_button_details_margin' );
		if ( is_array( $button_details_margin ) ) {
			foreach ( $button_details_margin as $margin => $value ) {
				if ( (int) $value > 0 ) {
					$styles .= ' margin-' . $margin . ': ' . (int) $value . 'px !important;';
				}
			}
		}

		$button_details_padding = get_option( 'izi_button_details_padding' );
		if ( is_array( $button_details_padding ) ) {
			foreach ( $button_details_padding as $padding => $value ) {
				if ( (int) $value > 0 ) {
					$styles .= ' padding-' . $padding . ': ' . (int) $value . 'px !important;';
				}
			}
		}

		$this->display(
			$id,
			$styles,
			esc_attr( get_option( 'izi_background' ) ) == 'dark',
			esc_attr( get_option( 'izi_variant' ) ) == 'primary',
			true,
			esc_attr( get_option( 'izi_align_details' ) ),
			InPostIzi::BINDING_PLACE_PRODUCT_CARD,
			esc_attr( get_option( 'izi_frame_style' ) ),
			esc_attr( get_option( 'izi_button_details_max_width' ) ),
			esc_attr( get_option( 'izi_button_details_min_height' ) ),
		);
	}

	public function displayCart( $id = null, $styles = '' ) {
		if ( ! $this->canDisplayInCart() ) {
			return;
		}
		$button_cart_margin = get_option( 'izi_button_cart_margin' );
		if ( is_array( $button_cart_margin ) ) {
			foreach ( $button_cart_margin as $margin => $value ) {
				if ( (int) $value > 0 ) {
					$styles .= ' margin-' . $margin . ': ' . (int) $value . 'px !important;';
				}
			}
		}

		$button_cart_padding = get_option( 'izi_button_cart_padding' );
		if ( is_array( $button_cart_padding ) ) {
			foreach ( $button_cart_padding as $padding => $value ) {
				if ( (int) $value > 0 ) {
					$styles .= ' padding-' . $padding . ': ' . (int) $value . 'px !important;';
				}
			}
		}

		$this->display(
			null,
			$styles,
			esc_attr( get_option( 'izi_background' ) ) == 'dark',
			esc_attr( get_option( 'izi_variant' ) ) == 'primary',
			true,
			esc_attr( get_option( 'izi_align_basket' ) ),
			InPostIzi::BINDING_PLACE_BASKET_SUMMARY,
			esc_attr( get_option( 'izi_frame_style' ) ),
			esc_attr( get_option( 'izi_button_cart_max_width' ) ),
			esc_attr( get_option( 'izi_button_cart_min_height' ) ),
		);
	}

	public function displayOrder( $id = null, $styles = '' ) {
		$this->display(
			null,
			$styles,
			esc_attr( get_option( 'izi_background' ) ) == 'dark',
			esc_attr( get_option( 'izi_variant' ) ) == 'primary',
			false,
			esc_attr( get_option( 'izi_align_order' ) ),
			InPostIzi::BINDING_PLACE_ORDER_CREATE,
			esc_attr( get_option( 'izi_frame_style' ) ),
			esc_attr( get_option( 'izi_button_order_max_width' ) ),
			esc_attr( get_option( 'izi_button_order_min_height' ) ),
		);
	}

	public function displayCheckout( $id = null, $styles = '' ) {
		$this->display(
			null,
			$styles,
			esc_attr( get_option( 'izi_background' ) ) == 'dark',
			esc_attr( get_option( 'izi_variant' ) ) == 'primary',
			false,
			esc_attr( get_option( 'izi_align_checkout' ) ),
			InPostIzi::BINDING_PLACE_CHECKOUT_PAGE,
			esc_attr( get_option( 'izi_frame_style' ) ),
			esc_attr( get_option( 'izi_button_checkout_max_width' ) ),
			esc_attr( get_option( 'izi_button_checkout_min_height' ) ),
		);
	}

	public function displayLoginPage( $id = null, $styles = '' ) {
		$this->display(
			null,
			$styles,
			esc_attr( get_option( 'izi_background' ) ) == 'dark',
			esc_attr( get_option( 'izi_variant' ) ) == 'primary',
			false,
			esc_attr( get_option( 'izi_align_login_page' ) ),
			InPostIzi::BINDING_PLACE_LOGIN_PAGE,
			esc_attr( get_option( 'izi_frame_style' ) ),
			esc_attr( get_option( 'izi_button_login_page_max_width' ) ),
			esc_attr( get_option( 'izi_button_login_page_min_height' ) ),
		);
	}

	public function displayMinicart( $id = null, $styles = '' ) {
		$this->display(
			null,
			$styles,
			esc_attr( get_option( 'izi_background' ) ) == 'dark',
			esc_attr( get_option( 'izi_variant' ) ) == 'primary',
			false,
			esc_attr( get_option( 'izi_align_minicart' ) ),
			InPostIzi::BINDING_PLACE_MINICART_PAGE,
			esc_attr( get_option( 'izi_frame_style' ) ),
			esc_attr( get_option( 'izi_button_minicart_max_width' ) ),
			esc_attr( get_option( 'izi_button_minicart_min_height' ) ),
		);
	}

	public function display(
		$id,
		$styles,
		$dark,
		$yellow,
		$cart,
		$align,
		$place,
		$frameStyle,
		$maxWidth,
		$minHeight
	) {
		if ( ! WC()->cart ) {
			CartSession::initiateWCCart();
		}

		if ( ! is_numeric( $id ) ) {
			$id = null;
		} else {
			if ( ! $this->iziAvailableForProduct( $id ) ) {
				return;
			}
		}
		$styles .= 'clear:both;';

		if ( ! empty( $styles ) ) {
			echo '<div style="' . $styles . '">';
		}


		InPostIzi::render(
			$id,
			true,
			false,
			'',
			\WC()->cart->get_cart_contents_count(),
			$dark,
			$yellow,
			$cart,
			$align,
			$place,
			$frameStyle,
			$maxWidth,
			$minHeight
		);
		if ( ! empty( $styles ) ) {
			echo '</div>';
		}
	}

	protected function iziAvailableForProduct( $id ): bool {
		$product = wc_get_product( $id );
		if ( $product->is_virtual() ) {
			// Product is virtual
			Logger::log( '[Display] Product is virtual: ' . $id );

			return false;
		}

		$configuredMethods = [
			explode( ':', esc_attr( get_option( 'izi_transport_method_apm' ) ) )[0],
			explode( ':', esc_attr( get_option( 'izi_transport_method_courier' ) ) )[0],
		];

		if ( get_option( 'izi_check_shipping_availability' ) == false ) {
			return true;
		}

		$allowedMethods = get_post_meta( $id, 'woo_inpost_shipping_methods_allowed', true );
		if ( is_array( $allowedMethods ) ) {
			$found = false;
			foreach ( $allowedMethods as $method ) {
				$method = explode( ':', $method )[0];
				if ( in_array( $method, $configuredMethods ) ) {
					$found = true;
				}
			}

			if ( ! $found ) {
				Logger::log( '[Display] Not found shipping methods for product: ' . $id );
			}

			return $found;
		}

		return true;
	}

	protected function canDisplayInCart(): bool {
		if ( ! WC()->cart ) {
			CartSession::initiateWCCart();
		}

		$cart_contents = WC()->cart->get_cart_contents();

		foreach ( $cart_contents as $cart_item ) {
			$product = wc_get_product( $cart_item['product_id'] );
			if ( $product->is_virtual() ) {
				Logger::log( '[Display In Cart] Product is virtual: ' . $cart_item['product_id'] );

				return false;
			}
		}

		return true;
	}
}
