<?php
/**
 * =================================================
 * Hook autozpro_page
 * =================================================
 */
add_action('autozpro_page', 'autozpro_page_header', 10);
add_action('autozpro_page', 'autozpro_page_content', 20);

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
add_action('autozpro_single_post', 'autozpro_post_header', 10);
add_action('autozpro_single_post', 'autozpro_post_thumbnail', 20);
add_action('autozpro_single_post', 'autozpro_post_content', 30);

/**
 * =================================================
 * Hook autozpro_single_post_bottom
 * =================================================
 */
add_action('autozpro_single_post_bottom', 'autozpro_post_taxonomy', 5);
add_action('autozpro_single_post_bottom', 'autozpro_post_nav', 10);
add_action('autozpro_single_post_bottom', 'autozpro_display_comments', 20);

/**
 * =================================================
 * Hook autozpro_loop_post
 * =================================================
 */
add_action('autozpro_loop_post', 'autozpro_post_header', 15);
add_action('autozpro_loop_post', 'autozpro_post_content', 30);

/**
 * =================================================
 * Hook autozpro_footer
 * =================================================
 */
add_action('autozpro_footer', 'autozpro_footer_default', 20);

/**
 * =================================================
 * Hook autozpro_after_footer
 * =================================================
 */

/**
 * =================================================
 * Hook wp_footer
 * =================================================
 */
add_action('wp_footer', 'autozpro_template_account_dropdown', 1);
add_action('wp_footer', 'autozpro_mobile_nav', 1);

/**
 * =================================================
 * Hook wp_head
 * =================================================
 */
add_action('wp_head', 'autozpro_pingback_header', 1);

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
add_action('autozpro_sidebar', 'autozpro_get_sidebar', 10);

/**
 * =================================================
 * Hook autozpro_loop_after
 * =================================================
 */
add_action('autozpro_loop_after', 'autozpro_paging_nav', 10);

/**
 * =================================================
 * Hook autozpro_page_after
 * =================================================
 */
add_action('autozpro_page_after', 'autozpro_display_comments', 10);

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

/**
 * =================================================
 * Hook autozpro_woocommerce_shop_loop_item_title
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_woocommerce_after_shop_loop_item_title
 * =================================================
 */

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

/**
 * =================================================
 * Hook autozpro_woocommerce_shop_loop_item_caption
 * =================================================
 */

/**
 * =================================================
 * Hook autozpro_woocommerce_shop_loop_item_caption_bottom
 * =================================================
 */
