<?php

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}

?>
<li <?php wc_product_class('product', $product); ?>>
    <div class="product-block-list">
        <div class="left">
            <?php
            /**
             * Functions hooked in to autozpro_woocommerce_list_item_title action
             *
             */
            do_action('autozpro_woocommerce_list_item_title');
            ?>
            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="menu-thumb">
                <?php echo wp_kses_post($product->get_image()); ?>
            </a>
        </div>
        <div class="right">
            <?php
            /**
             * Functions hooked in to autozpro_woocommerce_list_item_content action
             *
             * @see woocommerce_template_loop_price - 10 - woo
             * @see woocommerce_template_loop_product_title - 20 - woo
             * @see woocommerce_template_loop_rating - 30 - woo
             */
            do_action('autozpro_woocommerce_list_item_content');
            ?>
        </div>
    </div>
</li>
