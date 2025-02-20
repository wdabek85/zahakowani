<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\Integration\Basket\Availability\ProductIsEmptyException;
use Ilabs\Inpost_Pay\Integration\Basket\CartItemFilter;
use Ilabs\Inpost_Pay\Integration\Basket\ProductThirdPartyFilter;
use Ilabs\Inpost_Pay\Integration\Basket\Quantity\QuantityIntegrationFactory;
use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\BrowserIdStorage;
use Ilabs\Inpost_Pay\Lib\config\payment\PaymentMethodsInterface;
use Ilabs\Inpost_Pay\Lib\config\payment\PaymentMethodsOptions;
use Ilabs\Inpost_Pay\Lib\Coupons\PromotionsAvailable;
use Ilabs\Inpost_Pay\Lib\exception\CookieEmptyValueException;
use Ilabs\Inpost_Pay\Lib\helpers\CookieHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Lib\item\Basket;
use Ilabs\Inpost_Pay\Lib\item\MerchantStore;
use Ilabs\Inpost_Pay\Lib\item\Price;
use Ilabs\Inpost_Pay\Lib\item\Product;
use Ilabs\Inpost_Pay\Lib\item\ProductAttribute;
use Ilabs\Inpost_Pay\Lib\item\PromoCode;
use Ilabs\Inpost_Pay\Lib\item\Quantity;
use Ilabs\Inpost_Pay\Lib\item\Summary;
use Ilabs\Inpost_Pay\Lib\item\Variant;

use Ilabs\Inpost_Pay\Lib\omnibus\Coupon_Helper;
use Ilabs\Inpost_Pay\Lib\omnibus\Lowest_Price_Cache_Post_Meta_Repository;
use Ilabs\Inpost_Pay\Lib\omnibus\Price_Model;
use Ilabs\Inpost_Pay\Lib\omnibus\Product_Service;
use Ilabs\Inpost_Pay\models\CartSession;
use stdClass;
use WC_Product;
use WC_Tax;
use function WC;

class WooCommerceBasket extends IziJsonResponse {
	protected $basketBasePriceNet = 0;
	protected $basketBasePriceGross = 0;
	protected $basketBasePriceVat = 0;

	protected $basketPromoPriceNet = 0;
	protected $basketPromoPriceGross = 0;
	protected $basketPromoPriceVat = 0;

	protected $orderPromoPriceNet = 0;
	protected $orderPromoPriceGross = 0;
	protected $orderPromoPriceVat = 0;

	protected $relatedProductIds = [];

	public static $hasCoupons = false;
	public static $couponError = false;

	public static function getBasket( $refresh = true ): Basket {
		Logger::log( 'creating basket' );
		$wooCommerceBasket = new self();

		return $wooCommerceBasket->mapBasket( WC(), $refresh );
	}

	public function mapBasket( $wooCommerce, $refresh = true ): Basket {
		global $wp_actions;
		$basket = new Basket();

		if ( $refresh ) {
			$resetPostcode = false;
			$resetCity     = false;
			if ( empty( WC()->customer->get_shipping_country() ) ) {
				$resetPostcode = true;
				WC()->customer->set_shipping_country( 'PL' );
			}
			if ( empty( WC()->customer->get_shipping_postcode() ) ) {
				$resetPostcode = true;
				WC()->customer->set_shipping_postcode( '00-000' );
			}
			if ( empty( WC()->customer->get_shipping_city() ) ) {
				$resetCity = true;
				WC()->customer->set_shipping_city( 'Warszawa' );
			}

			$cartContents = ( ! WC()->cart || WC()->cart->is_empty() ) ? [] : $wooCommerce->cart->cart_contents;

			$tc = false;
			if ( ! empty( $cartContents ) ) {
				foreach ( $cartContents as $key => $item ) {
					if ( ! empty( $item['tmhasepo'] ) ) {
						if ( ! isset( $item['tm_epo_options_static_prices_first'] ) ) {
							if ( class_exists( 'THEMECOMPLETE_EPO_Cart' ) ) {
								$epo_cart             = \THEMECOMPLETE_EPO_Cart::instance();
								$cartContents[ $key ] = $epo_cart->add_cart_item( $item, $key );
							}
						}
						$cartContents[ $key ]['tc_recalculate'] = true;
						$tc                                     = true;
					}
				}

				if ( $tc === true && isset( $wp_actions['woocommerce_before_calculate_totals'] ) ) {
					unset ( $wp_actions['woocommerce_before_calculate_totals'] );
				}

				\WC()->cart->cart_contents = $cartContents;
			}

			WC()->cart->calculate_totals();
		}


		$cartContents = ( ! WC()->cart || WC()->cart->is_empty() ) ? [] : $wooCommerce->cart->cart_contents;

		if ( ! empty( $cartContents ) ) {
			CartSession::storeCurrent();
		}


		$basket->products    = $this->mapProducts( $cartContents );
		$basket->summary     = $this->mapSummary( $wooCommerce );
		$basket->promo_codes = $this->mapPromoCodes( $wooCommerce->cart );
		$wooDeliveryPrice    = new WooDeliveryPrice();
		$basket->delivery    = $wooDeliveryPrice->mapDelivery();
		$basket->consents    = $this->mapConsents();

		$basket->related_products = $this->mapRelatedProducts();
		//TODO: Disabled due to errors
//		$promotions_available = (new PromotionsAvailable())->get_coupons();
//		 if ($promotions_available) {
//			 $basket->promotions_available = $promotions_available;
//		 }


//		try {
//			//$basket->merchant_store = new MerchantStore();
//		} catch (CookieEmptyValueException $ex ) {
//
//		}


		$browserId = BrowserIdStorage::get();
		Logger::debug( 'BROWSER_ID: ' . $browserId );
		if ( $browserId ) {
			$basket->browser_id = $browserId;
		}

//		if ( $resetPostcode ) {
//			WC()->customer->set_shipping_postcode( '' );
//		}
//		if ( $resetCity ) {
//			WC()->customer->set_shipping_city( '' );
//		}


		Logger::log( $basket );

		return $basket;
	}

	public function mapProducts( $cartContents ): array {
		$array = [];
		Logger::log( $cartContents );
		if ( $cartContents && count( $cartContents ) ) {
			$cartItemFilter = new CartItemFilter();
			foreach ( $cartContents as $cartContent ) {
				/*if ( ! $cartItemFilter->canAddCartItem( $cartContent ) ) {
					continue;
				}*/

				if ( $this->canAddProduct( $cartContent ) ) {
					$array[] = $this->mapCartProduct( $cartContent );
				}
			}
		}

		$this->relatedProductIds = array_unique( $this->relatedProductIds );
		foreach ( $array as $product ) {
			if ( ( $key = array_search( $product->product_id,
					$this->relatedProductIds ) ) !== false ) {
				unset( $this->relatedProductIds[ $key ] );
				$this->relatedProductIds = array_values( $this->relatedProductIds );
			}
		}

		return $array;
	}


	public function canAddProduct( $cart_item ): bool {
		try {
			$availability = ( new Integration\Basket\Availability\AvailabilityProductFactory() )->create( $cart_item );
		} catch ( ProductIsEmptyException $e ) {
			return false;
		}

		return $availability->checkAvailability();

	}

	public function mapCartProduct( $cartContent ): Product {
		/**
		 * @var WC_Product $productSimple
		 */
		$productSimple = $cartContent["data"];


		$product = $this->mapProductData( $this->getProductIdentifier( $cartContent ),
			$cartContent );
		$this->collectRelatedProductIds( $productSimple );

		$product->quantity    = $this->readQuantity( $cartContent );
		$product->base_price  = $this->readCartProductBasePrice( $cartContent );
		$product->promo_price = $this->readCartProductPromoPrice( $cartContent );

		if ( ! WC()->cart || WC()->cart->is_empty() ) {
			$promo_codes_added = false;
		} else {
			$promo_codes_added = Coupon_Helper::validate_cart_having_omnibus_coupons( WC()->cart );
		}

		if ( inpost_pay()->omnibus_enabled() && $promo_codes_added ) {
			$omnibus_price = $this->readCartProductLowestPrice( $cartContent );
			if ( $omnibus_price ) {
				$product->lowest_price = $omnibus_price;

				inpost_pay()
					->get_omnibus()
					->get_woocommerce_logger( 'Omnibus' )
					->log_debug(
						sprintf( "[WooCommerceBasket] [mapCartProduct] [lowest_price: %s] [tax_status: %s]",
							print_r( $product->lowest_price, true ),
							print_r( $productSimple->is_taxable(), true ),
						) );
			}
		}

		Logger::log( $product );

		return $product;
	}

	public function getProductIdentifier( $item ) {
		if ( isset( $item['key'] ) ) {
			return $item['data']->get_id() . ':' . $item['key'];
		}

		return $item['data']->get_id();
	}

	public function readCartProductBasePrice( $cartContent ): Price {
		$productSimple = $cartContent["data"];
		$quantity      = $cartContent['quantity'];
		$price         = new Price();

		$priceIncludingTax = wc_get_price_including_tax( $productSimple, [ "price" => $productSimple->get_regular_price() ] );
		$priceExcludingTax = wc_get_price_excluding_tax( $productSimple, [ "price" => $productSimple->get_regular_price() ] );
		$vat               = $priceIncludingTax - $priceExcludingTax;

		$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
		$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
		$price->vat   = number_format( $vat, 2, '.', '' );

		$this->basketBasePriceNet   += $priceExcludingTax * $quantity;
		$this->basketBasePriceGross += $priceIncludingTax * $quantity;
		$this->basketBasePriceVat   += $vat * $quantity;

		return $price;
	}

	public function readCartProductLowestPrice( $cartContent ): ?Price {
		/**
		 * @var WC_Product $productSimple
		 */

		$productSimple = $cartContent["data"];
		$price         = new Price();

		$lowestPriceCacheRepository = new Lowest_Price_Cache_Post_Meta_Repository();
		$lowestPrice                = $lowestPriceCacheRepository
			->get( $productSimple->get_id() );

		if ( ! $lowestPrice instanceof Price_Model ) {
			return null;
		}

		$is_taxable = $productSimple->is_taxable();

		if ( $is_taxable ) {
			$priceIncludingTax = $lowestPrice->get_price_float();
			$priceExcludingTax = wc_get_price_excluding_tax( $productSimple,
				[ "price" => $lowestPrice->get_price_float() ] );
		} else {
			$priceIncludingTax = wc_get_price_including_tax( $productSimple,
				[ "price" => $lowestPrice->get_price_float() ] );
			$priceExcludingTax = $lowestPrice->get_price_float();
		}

		$taxes = WC_Tax::calc_tax( $priceIncludingTax,
			WC_Tax::get_shipping_tax_rates(),
			true );

		$vat = array_sum( $taxes );

		$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
		$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
		$price->vat   = number_format( $vat, 2, '.', '' );

		return $price;
	}


	public function readCartProductPromoPrice( $item ): Price {
		/**
		 * @var WC_Product $productSimple
		 */
		$productSimple = $item["data"];
		$quantity      = $item['quantity'];


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

		$lineTotalKey = 'subtotal';

		if ( ! isset( $item['line_tax_data'][ $lineTotalKey ] ) ) {
			$item['line_tax_data'][ $lineTotalKey ] = [];
		}

		$price = new Price();

		$tax_total = \WC_Tax::get_tax_total( $item['line_tax_data'][ $lineTotalKey ] );

		$productQuantity = ( new QuantityIntegrationFactory() )->create( $productSimple );

		if ( $productQuantity->get_quantity_type() === 'DECIMAL' ) {
			$priceIncludingTax = ( $item["line_$lineTotalKey"] + $tax_total );
			$priceExcludingTax = $item["line_$lineTotalKey"];
			$vat               = $tax_total;
		} else {
			$priceIncludingTax = ( $item["line_$lineTotalKey"] + $tax_total ) / $quantity;
			$priceExcludingTax = $item["line_$lineTotalKey"] / $quantity;
			$vat               = $tax_total / $quantity;
		}


		$this->basketPromoPriceNet   += $item["line_$lineTotalKey"];
		$this->basketPromoPriceGross += $item["line_$lineTotalKey"] + $tax_total;
		$this->basketPromoPriceVat   += $tax_total;

		$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
		$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
		$price->vat   = number_format( $vat, 2, '.', '' );

		$this->orderPromoPriceNet   += $priceExcludingTax;
		$this->orderPromoPriceGross += $priceIncludingTax;
		$this->orderPromoPriceVat   += $vat;

		return $price;
	}

	public function mapProductData( $productIdentifier, $cartContent ): Product {
		if ( $cartContent instanceof WC_Product ) {
			$productSimple = $cartContent;
		} else {
			$productSimple = $cartContent['data'];
		}

		$product = new Product();

		$product->product_id = $productIdentifier;
		if ( isset( $productSimple->get_category_ids()[0] ) ) {
			$product->product_category = $productSimple->get_category_ids()[0];
		}
		$product->ean                 = $productSimple->get_sku() ?: '0';
		$product->product_name        = strip_tags( html_entity_decode( $productSimple->get_name() ) );
		$product->product_description = $this->formatProductDescription( $this->getDescriptionByWcProduct( $productSimple ) );

		if ( ! $product->product_description && $productSimple->get_parent_id() ) {
			$parent = wc_get_product( $productSimple->get_parent_id() );
			if ( $parent ) {

				$product->product_description = $this->formatProductDescription( $this->getDescriptionByWcProduct( $parent ) );
			}
		}
		$product->product_link = $productSimple->get_permalink();

		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $productSimple->get_id() ), 'single-post-thumbnail' );
		if ( ! $image && $productSimple->get_parent_id() ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $productSimple->get_parent_id() ), 'single-post-thumbnail' );
		}

		if ( $image && $image[0] ) {
			$product->product_image = str_replace( "http://", "https://", $image[0] );
		} else {
			$product->product_image = '';
		}

		$product->additional_product_images = $this->getProductImages( $productSimple );


		$product->variants           = $this->mapProductVariables( $productSimple );
		$product->product_attributes = ( $cartContent instanceof WC_Product ) ? [] : $this->mapProductAttributes( $cartContent );

		return $product;
	}

	public function getProductImages( $wcProduct ): array {
		$images = [];
		$gallery_image_ids = $wcProduct->get_gallery_image_ids();

		if ( ! empty( $gallery_image_ids ) ) {
			foreach ( $gallery_image_ids as $gallery_image_id ) {
				$image = new stdClass();
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

	private function formatProductDescription( ?string $description
	): string {
		if ( empty( $description ) ) {
			return '';
		}

		$description = $this->removeUnregisteredShortcodes( $description );

		$description = do_shortcode( $description );

		for ( $i = 1; $i <= 6; $i ++ ) {
			$description = str_replace( "<h" . $i . ">",
				"\r\n<h" . $i . ">",
				$description );
		}

		$description = $this->replaceLiteralNewlines( $description );

		$description = trim( $description );
		$description = strip_tags( $description );

		return $description;
	}

	private function removeUnregisteredShortcodes( $content ) {
		global $shortcode_tags;

		$matches_block = [];

		// find block shortcodes
		preg_match_all( '/[\/?\w+[^\]]*].*[\/?\w+[^\]]*]/',
			$content,
			$matches_block,
			PREG_SET_ORDER );


		$to_remove_blocks = [];
		foreach ( $matches_block as $match ) {
			$pattern = '/\[([\w_]+)].*?\[\/\1]/';

			// Use preg_match to find the shortcode
			if ( isset( $match[1] ) && preg_match( $pattern, $match[1], $matches ) ) {
				// Return the ID (which is the first capturing group)

				if ( isset( $matches[1] ) && ! isset( $shortcode_tags[ $matches[1] ] ) ) {
					$to_remove_blocks[] = $matches[1];
				}
			}
		}

		if ( ! empty( $to_remove_blocks ) ) {
			$pattern = get_shortcode_regex( $to_remove_blocks );
			$content = preg_replace_callback( "/$pattern/",
				'strip_shortcode_tag',
				$content );
		}

		// find inline shortcodes
		preg_match_all( '/\[(\w+).*]/',
			$content,
			$matches_inline,
			PREG_SET_ORDER );


		$to_remove_inline = [];
		foreach ( $matches_inline as $match ) {
			if ( isset($match[1]) && ! isset( $shortcode_tags[ $match[1] ] ) ) {
				$to_remove_inline[] = $match[1];
			}
		}

		if ( ! empty( $to_remove_inline ) ) {
			$pattern = get_shortcode_regex( $to_remove_inline );
			$content = preg_replace_callback( "/$pattern/",
				'strip_shortcode_tag',
				$content );
		}

		return $content;
	}

	private function replaceLiteralNewlines( $input ) {
		return preg_replace( '/\\\\n/', "\r\n", $input );
	}

	private function getDescriptionByWcProduct( $product
	): string {
		if ( ! $product instanceof \WC_Product ) {
			return '';
		}

		if ( SettingsPage::OPT_DROPDOWN_ID_SHORT_PRODUCT_DESC_MAP === get_option( SettingsPage::OPT_KEY_PRODUCT_DESC_MAP,
				SettingsPage::OPT_DROPDOWN_ID_DEFAULT_PRODUCT_DESC_MAP ) ) {
			return $product->get_short_description();
		}
		$description = $product->get_description();

		return '' === $description ? $product->get_short_description() : $description;
	}

	public function mapProductVariables( $productSimple ): array {
		$array = [];
		if ( $productSimple->get_parent_id() ) {
			$productSimple = wc_get_product( $productSimple->get_parent_id() );
		}
		foreach ( $productSimple->get_attributes() as $attribute ) {
			if ( $attribute->get_visible() && $attribute->get_variation() === true ) {
				$array[] = $this->mapProductVariable( $attribute );
			}
		}

		return $array;
	}

	public function mapProductVariable( $attribute ): Variant {
		$variant = new Variant();

		$variant->variant_id     = $attribute->get_id();
		$variant->variant_name   = wc_attribute_label( $attribute->get_name() ) ?: $attribute->get_name();
		$variant->variant_values = implode( ", ", $attribute->get_options() );

		$variant->variant_description = "";
		$variant->variant_type        = "";

		return $variant;
	}

	public function mapProductAttributes( $cart_item ): array {
		$array     = [];
		$item_data = [];

		// Variation values are shown only if they are not found in the title as of 3.0.
		// This is because variation titles display the attributes.
		if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
			foreach ( $cart_item['variation'] as $name => $value ) {
				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

				if ( taxonomy_exists( $taxonomy ) ) {
					// If this is a term slug, get the term's nice name.
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
					$label = wc_attribute_label( $taxonomy );
				} else {
					// If this is a custom option slug, get the options name.
					$value = apply_filters( 'woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data'] );
					$label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] );
				}

				// Check the nicename against the title.
				if ( '' === $value || wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
					continue;
				}

				if ( ! strlen( $value ) ) {
					continue;
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => $value,
				);
			}
		}

		// Filter item data to allow 3rd parties to add more to the array.
		$item_data = apply_filters( 'woocommerce_get_item_data', $item_data, $cart_item );

		if ( is_array( $item_data ) ) {
			// Format item data ready to display.
			foreach ( $item_data as $key => $data ) {
				// Set hidden to true to not display meta on cart.
				if ( ! empty( $data['hidden'] ) ) {
					unset( $item_data[ $key ] );
					continue;
				}
				$item_data[ $key ]['key']     = ! empty( $data['key'] ) ? $data['key'] : $data['name'];
				$item_data[ $key ]['display'] = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
			}

			// Output flat or in list format.
			if ( count( $item_data ) > 0 ) {
				foreach ( $item_data as $data ) {
					if ( strlen( strip_tags( wp_kses_post( $data['display'] ) ) ) > 1 ) {
						$array[] = $this->mapProductAttribute( $data['key'], wp_kses_post( $data['display'] ) );
					}
				}
			}
		}

		return $array;
	}

	public function mapProductAttribute( $name, $value ): ProductAttribute {
		return new ProductAttribute( $name, $value );
	}

	public function readQuantity( $cartContent ): Quantity {
		$quantity = $this->readStockQuantity( $cartContent["data"] );

		$quantity->quantity = ( intval( $cartContent['quantity'] ) == $cartContent['quantity'] ) ? $cartContent['quantity'] : number_format( $cartContent['quantity'], 2, '.', '' );

		return $quantity;
	}

	public function readStockQuantity( \WC_Product $productSimple ): Quantity {
		$quantity = new Quantity();

		$productQuantity = ( new QuantityIntegrationFactory() )->create( $productSimple );

		$quantity->quantity_type = $productQuantity->get_quantity_type();
		$quantity->quantity_unit = $productQuantity->get_quantity_unit();
		$quantity->quantity_jump = $productQuantity->get_step_quantity();
		Logger::debug( '[Add to Basket] Stock quantity: ' . serialize( $productSimple ) );

		$availableQuantity = $productQuantity->get_quantity();


		/*if ( ( new ProductThirdPartyFilter() )
			->quantityModificationLockIsRequired( $productSimple ) ) {
			$availableQuantity = 1;
		}*/

		if ( $availableQuantity ) {
			$quantity->available_quantity = $availableQuantity;
		} else {
			$quantity->available_quantity = 999;
		}

		if ( $quantity->available_quantity <= 0 ) {
			if ( $productSimple->is_on_backorder() || $productSimple->get_backorders( false ) === 'notify' || $productSimple->get_backorders( false ) === 'yes' ) {
				$quantity->available_quantity = 999;
			}
		}

		$maxQuantity = $productSimple->get_max_purchase_quantity();
		if ( $maxQuantity !== - 1 ) {
			$quantity->max_quantity = $maxQuantity;
		} else {
			$quantity->max_quantity = 999;
		}

		return $quantity;
	}

	public function mapPromoCodes( \WC_Cart $cart ): array {
		$array = [];

		if ( $cart ) {
			foreach ( $cart->get_applied_coupons() as $coupon ) {
				array_push( $array, $this->mapPromoCode( $coupon ) );
			}
		}

		return $array;
	}

	public function mapPromoCode( $code ): PromoCode {
		$promoCode = new PromoCode();
		$coupon    = new \WC_Coupon( $code );

		$promoCode->name             = $coupon->get_description();
		$promoCode->promo_code_value = $coupon->get_code();
		if ( ! $promoCode->name ) {
			$promoCode->name = $promoCode->promo_code_value;
		}

		return $promoCode;
	}

	public function mapSummary( $wooCommerce ): Summary {
		$summary = new Summary();

		$summary->basket_base_price  = $this->readSummaryBasketBasePrice();
		$summary->basket_promo_price = $this->readSummaryBasketPromoPrice();
		$summary->basket_final_price = $this->readSummaryBasketFinalPrice( $summary->basket_promo_price );

		$summary->currency                      = get_woocommerce_currency();
		$summary->basket_expiration_date        = $this->readBasketExpirationDate();
		$summary->basket_additional_information = '';
		$summary->payment_type                  = $this->readPaymentType();

		if ( self::$hasCoupons ) {
			if ( self::$couponError ) {
				$summary->basket_notice = [
					'type'        => 'ERROR',
					'description' => 'Kod jest nieaktywny lub nieprawidłowy',
				];
			} else {
				$summary->basket_notice = [
					'type'        => 'ATTENTION',
					'description' => 'Kod został aktywowany',
				];
			}
		}

		return $summary;
	}

	public function readBasketExpirationDate(): string {
		return CookieHelper::readSessionExpirationDate();
	}

	public function readSummaryBasketBasePrice(): Price {
		$price = new Price();

		$price->gross = number_format( $this->basketBasePriceGross, 2, '.', '' );
		$price->net   = number_format( $this->basketBasePriceNet, 2, '.', '' );
		$price->vat   = number_format( $this->basketBasePriceVat, 2, '.', '' );

		return $price;
	}

	public function readSummaryBasketPromoPrice(): Price {
		$price = new Price();

		$price->gross = number_format( $this->basketPromoPriceGross, 2, '.', '' );
		$price->net   = number_format( $this->basketPromoPriceNet, 2, '.', '' );
		$price->vat   = number_format( $this->basketPromoPriceVat, 2, '.', '' );

		return $price;
	}

	public function readSummaryBasketFinalPrice( $promoPrice ): Price {
		$price = new Price();

		WC()->cart->calculate_totals();
		$couponsNetWorth = array_sum( WC()->cart->get_coupon_discount_totals() );
		$couponsTaxWorth = array_sum( WC()->cart->get_coupon_discount_tax_totals() );
		$price->gross    = number_format( $promoPrice->gross - $couponsNetWorth - $couponsTaxWorth, 2, '.', '' );
		$price->vat      = number_format( $this->basketPromoPriceVat - $couponsTaxWorth, 2, '.', '' );
		if ( $promoPrice->gross == $promoPrice->net ) {
			$price->net = $price->gross;
		} else {
			$price->net = number_format( $price->gross - $price->vat, 2, '.', '' );
		}

		if ( $price->net <= 0 ) {
			$price->net = number_format( 0, 2, '.', '' );
		}
		if ( $price->gross <= 0 ) {
			$price->gross = number_format( 0, 2, '.', '' );
		}
		if ( $price->vat <= 0 ) {
			$price->vat = number_format( 0, 2, '.', '' );
		}

		return $price;
	}

	public function mapRelatedProducts(): array {
		$array = [];
		$max   = intval( esc_attr( get_option( 'izi_related_count' ) ) );
		if ( $max ) {
			$count = 0;
			foreach ( $this->relatedProductIds as $key ) {
				if ( ! $this->checkAvailability( $key ) ) {
					continue;
				}
				$count ++;
				array_push( $array, $this->mapRelatedProduct( $key ) );
				if ( $count >= $max ) {
					break;
				}
			}
		}

		return $array;
	}

	protected function checkAvailability( $product_id ): bool {
		$product = wc_get_product( $product_id );


		return ! empty( $product ) && $product->is_purchasable() && $product->is_in_stock() && $product->is_visible();
	}

	public function mapRelatedProduct( $productId ): Product {
		$productSimple        = wc_get_product( $productId );
		$product              = $this->mapProductData( $productId, $productSimple );
		$product->base_price  = $this->readRelatedProductBasePrice( $productSimple );
		$product->promo_price = $this->readRelatedProductPromoPrice( $productSimple );

		$product->quantity           = $this->readStockQuantity( $productSimple );
		$product->quantity->quantity = 1;

		return $product;
	}

	public function readRelatedProductBasePrice( $productSimple ): Price {
		$price = new Price();

		$priceIncludingTax = wc_get_price_including_tax( $productSimple, [ "price" => $productSimple->get_regular_price() ] );
		$priceExcludingTax = wc_get_price_excluding_tax( $productSimple, [ "price" => $productSimple->get_regular_price() ] );
		$vat               = $priceExcludingTax - $priceExcludingTax;

		$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
		$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
		$price->vat   = number_format( $vat, 2, '.', '' );

		return $price;
	}

	public function readRelatedProductPromoPrice( $productSimple ): Price {
		$price = new Price();

		$priceIncludingTax = wc_get_price_including_tax( $productSimple );
		$priceExcludingTax = wc_get_price_excluding_tax( $productSimple );
		$vat               = $priceExcludingTax - $priceExcludingTax;

		$price->net   = number_format( $priceExcludingTax, 2, '.', '' );
		$price->gross = number_format( $priceIncludingTax, 2, '.', '' );
		$price->vat   = number_format( $vat, 2, '.', '' );

		$this->basketPromoPriceNet   += $priceExcludingTax;
		$this->basketPromoPriceGross += $priceIncludingTax;
		$this->basketPromoPriceVat   += $vat;

		return $price;
	}

	public function collectRelatedProductIds( $productSimple ) {
		$parent = null;

		if ( $productSimple->get_parent_id() ) {
			$parent = wc_get_product( $productSimple->get_parent_id() );
		}
		if ( $parent ) {
			$relatedProducts = array_merge( $parent->get_cross_sell_ids(), $parent->get_upsell_ids() );
		} else {
			$relatedProducts = array_merge( $productSimple->get_cross_sell_ids(), $productSimple->get_upsell_ids() );
		}

		foreach ( $relatedProducts as $relatedProduct ) {
			$this->relatedProductIds[] = $relatedProduct;
		}
	}

	public function readPaymentType(): array {
		$methods = [];

		if ( intval( esc_attr( get_option( 'izi_payment_aion', 1 ) ) ) ) {
			$methods = ( new PaymentMethodsOptions() )->get();
			if ( ! is_array( $methods ) && count( $methods ) === 0 ) {
				$methods = PaymentMethodsInterface::IZI_PAYMENT_METHODS;
			}
		}
		if ( intval( esc_attr( get_option( 'izi_payment_inpost' ) ) ) ) {
			$methods[] = 'CASH_ON_DELIVERY';
		}


		return $methods;
	}

	public static function checkProductAvailability( $product_id ): bool {
		$product = wc_get_product( $product_id );
		if ( ! empty( $product ) && $product->is_purchasable() && $product->is_in_stock() && $product->is_visible() ) {
			return true;
		}

		return false;
	}
}
