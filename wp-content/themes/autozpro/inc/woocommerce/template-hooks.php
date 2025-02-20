<?php
/**
 * =================================================
 * Hook autozpro_page
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_single_post_top
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_single_post
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_single_post_bottom
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_loop_post
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_footer
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_after_footer
 * =================================================
 */
add_action('autozpro_after_footer', 'autozpro_sticky_single_add_to_cart', 999);

/**
 * =================================================
 * Hook wp_footer
 * =================================================
 */
add_action('wp_footer', 'autozpro_render_woocommerce_shop_canvas', 1);

/**
 * =================================================
 * Hook wp_head
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_before_header
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_before_content
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_content_top
 * =================================================
 */
add_action('autozpro_content_top', 'autozpro_shop_messages', 10);

/**
 * =================================================
 * Hook autozpro_post_content_before
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_post_content_after
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_sidebar
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_loop_after
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_page_after
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_woocommerce_list_item_title
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_woocommerce_list_item_content
 * =================================================
 */
add_action('autozpro_woocommerce_list_item_content', 'woocommerce_template_loop_price', 10);
add_action('autozpro_woocommerce_list_item_content', 'woocommerce_template_loop_product_title', 20);
add_action('autozpro_woocommerce_list_item_content', 'woocommerce_template_loop_rating', 30);

/**
 * =================================================
 * Hook autozpro_woocommerce_before_shop_loop_item
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_woocommerce_before_shop_loop_item_title
 * =================================================
 */
add_action('autozpro_woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action('autozpro_woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 15);

/**
 * =================================================
 * Hook autozpro_woocommerce_shop_loop_item_title
 * =================================================
 */
add_action('autozpro_woocommerce_shop_loop_item_title', 'woocommerce_template_loop_price', 5);
add_action('autozpro_woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('autozpro_woocommerce_shop_loop_item_title', 'autozpro_woocommerce_get_product_category', 15);
add_action('autozpro_woocommerce_shop_loop_item_title', 'woocommerce_template_loop_rating', 15);

/**
 * =================================================
 * Hook autozpro_woocommerce_after_shop_loop_item_title
 * =================================================
 */
add_action('autozpro_woocommerce_after_shop_loop_item_title', 'autozpro_woocommerce_get_product_description', 15);
add_action('autozpro_woocommerce_after_shop_loop_item_title', 'autozpro_woocommerce_product_list_bottom', 25);

/**
 * =================================================
 * Hook autozpro_woocommerce_after_shop_loop_item
 * =================================================
 */

/**
 * =================================================
 * Hook woocommerce_before_shop_loop_item
 * =================================================
 */

/**
 * =================================================
 * Hook woocommerce_before_shop_loop_item_title
 * =================================================
 */
add_action('woocommerce_before_shop_loop_item_title', 'autozpro_template_loop_product_thumbnail', 25);
add_action('woocommerce_before_shop_loop_item_title', 'autozpro_wishlist_button', 30);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 35);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 40);

/**
 * =================================================
 * Hook autozpro_woocommerce_shop_loop_item_caption
 * =================================================
 */
add_action('autozpro_woocommerce_shop_loop_item_caption', 'woocommerce_template_loop_price', 5);
add_action('autozpro_woocommerce_shop_loop_item_caption', 'woocommerce_template_loop_product_title', 10);
add_action('autozpro_woocommerce_shop_loop_item_caption', 'autozpro_woocommerce_get_product_category', 15);
add_action('autozpro_woocommerce_shop_loop_item_caption', 'woocommerce_template_loop_rating', 20);

/**
 * =================================================
 * Hook autozpro_woocommerce_shop_loop_item_caption_bottom
 * =================================================
 */
add_action('autozpro_woocommerce_shop_loop_item_caption_bottom', 'woocommerce_template_loop_add_to_cart', 5);
add_action('autozpro_woocommerce_shop_loop_item_caption_bottom', 'autozpro_right_button', 10);
