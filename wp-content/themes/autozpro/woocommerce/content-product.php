<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('product-style-default', $product); ?>>
    <?php
    /**
     * Functions hooked in to woocommerce_before_shop_loop_item action
     *
     */
    do_action('woocommerce_before_shop_loop_item');
    ?>
    <div class="product-block">
        <div class="content-product-imagin"></div>
        <div class="product-transition">

            <?php
            /**
             * Functions hooked in to woocommerce_before_shop_loop_item_title action
             *
             * @see autozpro_template_loop_product_thumbnail - 25 - woo
             * @see autozpro_wishlist_button - 30 - woo
             * @see woocommerce_template_loop_product_link_open - 35 - woo
             * @see woocommerce_template_loop_product_link_close - 40 - woo
             */
            do_action('woocommerce_before_shop_loop_item_title');
            ?>

        </div>
        <div class="product-caption">

            <?php
            /**
             * Functions hooked in to autozpro_woocommerce_shop_loop_item_caption action
             *
             * @see woocommerce_template_loop_price - 5 - woo
             * @see woocommerce_template_loop_product_title - 10 - woo
             * @see autozpro_woocommerce_get_product_category - 15 - woo
             * @see woocommerce_template_loop_rating - 20 - woo
             */
            do_action('autozpro_woocommerce_shop_loop_item_caption');
            ?>
        </div>
        <div class="product-caption-bottom">

            <?php
            /**
             * Functions hooked in to autozpro_woocommerce_shop_loop_item_caption_bottom action
             *
             * @see woocommerce_template_loop_add_to_cart - 5 - woo
             * @see autozpro_right_button - 10 - woo
             */
            do_action('autozpro_woocommerce_shop_loop_item_caption_bottom');
            ?>

        </div>
    </div>
    <?php do_action('woocommerce_after_shop_loop_item'); ?>
</li>
