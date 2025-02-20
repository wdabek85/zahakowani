<?php

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('product-list', $product); ?>>
    <?php
    /**
     * Functions hooked in to autozpro_woocommerce_before_shop_loop_item action
     *
     */
    do_action('autozpro_woocommerce_before_shop_loop_item');


    ?>
    <div class="product-image">
        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="woocommerce-loop-product__link">
        <?php
        /**
         * Functions hooked in to autozpro_woocommerce_before_shop_loop_item_title action
         *
		 * @see woocommerce_show_product_loop_sale_flash - 10 - woo
         * @see woocommerce_template_loop_product_thumbnail - 15 - woo
         */
        do_action('autozpro_woocommerce_before_shop_loop_item_title');
        ?>
        </a>
    </div>
    <div class="product-caption">
        <?php
        /**
         * Functions hooked in to autozpro_woocommerce_shop_loop_item_title action
         *
         * @see woocommerce_template_loop_price - 5 - woo
         * @see woocommerce_template_loop_product_title - 10 - woo
         * @see autozpro_woocommerce_get_product_category - 15 - woo
         * @see woocommerce_template_loop_rating - 15 - woo
         */
        do_action('autozpro_woocommerce_shop_loop_item_title');

        /**
         * Functions hooked in to autozpro_woocommerce_after_shop_loop_item_title action
         *
         * @see autozpro_woocommerce_get_product_description - 15 - woo
         * @see autozpro_woocommerce_product_list_bottom - 25 - woo
         *
         */
        do_action('autozpro_woocommerce_after_shop_loop_item_title');
        ?>
    </div>
    <?php
    /**
     * Functions hooked in to autozpro_woocommerce_after_shop_loop_item action
     *
     */
    do_action('autozpro_woocommerce_after_shop_loop_item');
    ?>
</li>
