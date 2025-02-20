<?php
/**
 * Checks if the current page is a product archive
 *
 * @return boolean
 */
function autozpro_is_product_archive() {
    if (is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag()) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param $product WC_Product
 */
function autozpro_product_get_image($product) {
    return $product->get_image();
}

/**
 * @param $product WC_Product
 */
function autozpro_product_get_price_html($product) {
    return $product->get_price_html();
}

/**
 * Retrieves the previous product.
 *
 * @param bool $in_same_term Optional. Whether post should be in a same taxonomy term. Default false.
 * @param array|string $excluded_terms Optional. Comma-separated list of excluded term IDs. Default empty.
 * @param string $taxonomy Optional. Taxonomy, if $in_same_term is true. Default 'product_cat'.
 * @return WC_Product|false Product object if successful. False if no valid product is found.
 * @since 2.4.3
 *
 */
function autozpro_get_previous_product($in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat') {
    $product = new Autozpro_WooCommerce_Adjacent_Products($in_same_term, $excluded_terms, $taxonomy, true);
    return $product->get_product();
}

/**
 * Retrieves the next product.
 *
 * @param bool $in_same_term Optional. Whether post should be in a same taxonomy term. Default false.
 * @param array|string $excluded_terms Optional. Comma-separated list of excluded term IDs. Default empty.
 * @param string $taxonomy Optional. Taxonomy, if $in_same_term is true. Default 'product_cat'.
 * @return WC_Product|false Product object if successful. False if no valid product is found.
 * @since 2.4.3
 *
 */
function autozpro_get_next_product($in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat') {
    $product = new Autozpro_WooCommerce_Adjacent_Products($in_same_term, $excluded_terms, $taxonomy);
    return $product->get_product();
}


function autozpro_is_woocommerce_extension_activated($extension = 'WC_Bookings') {
    if ($extension == 'YITH_WCQV') {
        return class_exists($extension) && class_exists('YITH_WCQV_Frontend') ? true : false;
    }

    return class_exists($extension) ? true : false;
}

function autozpro_woocommerce_pagination_args($args) {
    $args['prev_text'] = '<i class="autozpro-icon autozpro-icon-long-arrow-left"></i><span class="screen-reader-text">' . esc_html__('Previons', 'autozpro') . '</span>';
    $args['next_text'] = '<span class="screen-reader-text">' . esc_html__('Next', 'autozpro') . '</span><i class="autozpro-icon autozpro-icon-long-arrow-right"></i>';
    return $args;

}

add_filter('woocommerce_pagination_args', 'autozpro_woocommerce_pagination_args', 10, 1);


function autozpro_unsupported_theme_remove_review_tab($tabs) {
    unset($tabs['reviews']);
    return $tabs;
}

/**
 * Check if a product is a deal
 *
 * @param int|object $product
 *
 * @return bool
 */
function autozpro_woocommerce_is_deal_product($product) {
    $product = is_numeric($product) ? wc_get_product($product) : $product;

    // It must be a sale product first
    if (!$product->is_on_sale()) {
        return false;
    }

    if (!$product->is_in_stock()) {
        return false;
    }

    // Only support product type "simple" and "external"
    if (!$product->is_type('simple') && !$product->is_type('external')) {
        return false;
    }

    $deal_quantity = get_post_meta($product->get_id(), '_deal_quantity', true);

    if ($deal_quantity > 0) {
        return true;
    }

    return false;
}

function woocommerce_template_loop_rating() {
    global $product;
    if (!wc_review_ratings_enabled()) {
        return;
    }
    if ($rating_html = wc_get_rating_html($product->get_average_rating())) {
        echo apply_filters('autozpro_woocommerce_rating_html', '<div class="count-review">' . $rating_html . '<span>(' . number_format_i18n($product->get_review_count()) . ')</span></div>');
    } else {
        echo '<div class="count-review"><div class="star-rating"></div><span>(' . number_format_i18n($product->get_review_count()) . ')</span></div>';
    }
}

if (!function_exists('autozpro_check_quantity_product')) {
    function autozpro_check_quantity_product() {
        global $product;
        $quantity = get_post_meta($product->get_id(), '_sold_individually', true);
        if ($quantity == 'yes' || $product->get_stock_status() == 'outofstock' || $product->is_type('variable') || $product->is_type('grouped')) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('autozpro_ajax_search_products')) {
    function autozpro_ajax_search_products() {
        global $woocommerce;
        $search_keyword = $_REQUEST['query'];

        $ordering_args = $woocommerce->query->get_catalog_ordering_args('date', 'desc');
        $suggestions   = array();

        $args = array(
            's'                   => apply_filters('autozpro_ajax_search_products_search_query', $search_keyword),
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby'             => $ordering_args['orderby'],
            'order'               => $ordering_args['order'],
            'posts_per_page'      => apply_filters('autozpro_ajax_search_products_posts_per_page', 8),

        );

        $args['tax_query']['relation'] = 'AND';

        if (!empty($_REQUEST['product_cat'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => strip_tags($_REQUEST['product_cat']),
                'operator' => 'IN'
            );
        }

        $products = get_posts($args);

        if (!empty($products)) {
            foreach ($products as $post) {
                $product       = wc_get_product($post);
                $product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()));

                $suggestions[] = apply_filters('autozpro_suggestion', array(
                    'id'    => $product->get_id(),
                    'value' => strip_tags($product->get_title()),
                    'url'   => $product->get_permalink(),
                    'img'   => esc_url($product_image[0]),
                    'price' => $product->get_price_html(),
                ), $product);
            }
        } else {
            $suggestions[] = array(
                'id'    => -1,
                'value' => esc_html__('No results', 'autozpro'),
                'url'   => '',
            );
        }
        wp_reset_postdata();

        echo json_encode($suggestions);
        die();
    }
}

add_action('wp_ajax_autozpro_ajax_search_products', 'autozpro_ajax_search_products');
add_action('wp_ajax_nopriv_autozpro_ajax_search_products', 'autozpro_ajax_search_products');


if (function_exists('wpcpf_init')) {
    function autozpro_remove_wpcpf_tab($tabs) {
        unset($tabs['wpcpf']);
        return $tabs;
    }

    function autozpro_product_faqs($product_id) {
        wp_enqueue_script('jquery-ui-accordion');

        $content = '';
        $faqs    = [];

        // global faqs
        $args  = array(
            'post_type'    => 'wpc_product_faq',
            'meta_key'     => 'type',
            'meta_value'   => 'none',
            'meta_compare' => '!=',
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $type    = !empty(get_post_meta($post_id, 'type', true)) ? get_post_meta($post_id, 'type', true) : 'none';

                switch ($type) {
                    case 'all':
                        $faqs[] = array(
                            'type'    => 'all',
                            'title'   => get_the_title(),
                            'content' => get_the_content(),
                        );

                        break;
                    case 'categories':
                        if (!empty(get_post_meta($post_id, 'categories', true))) {
                            $categories = explode(',', get_post_meta($post_id, 'categories', true));

                            if (has_term($categories, 'product_cat', $product_id)) {
                                $faqs[] = array(
                                    'type'    => 'categories',
                                    'title'   => get_the_title(),
                                    'content' => get_the_content(),
                                );
                            }
                        }

                        break;
                    case 'tags':
                        if (!empty(get_post_meta($post_id, 'tags', true))) {
                            $tags = explode(',', get_post_meta($post_id, 'tags', true));

                            if (has_term($tags, 'product_tag', $product_id)) {
                                $faqs[] = array(
                                    'type'    => 'tags',
                                    'title'   => get_the_title(),
                                    'content' => get_the_content(),
                                );
                            }
                        }

                        break;
                }
            }

            wp_reset_postdata();
        }

        // product faqs
        $product_faqs = get_post_meta($product_id, 'wpcpf_faqs', true);

        if (!empty($product_faqs)) {
            foreach ($product_faqs as $product_faq) {
                if ($product_faq['type'] === 'global') {
                    if (!empty($product_faq['title'])) {
                        $global_ids = explode(',', $product_faq['title']);

                        foreach ($global_ids as $global_id) {
                            if ($global_faq = get_post($global_id)) {
                                $faqs[] = array(
                                    'type'    => esc_attr($product_faq['type']),
                                    'title'   => $global_faq->post_title,
                                    'content' => $global_faq->post_content
                                );
                            }
                        }
                    }
                } else {
                    if (!empty($product_faq['title']) && !empty($product_faq['content'])) {
                        $faqs[] = array(
                            'type'    => esc_attr($product_faq['type']),
                            'title'   => $product_faq['title'],
                            'content' => $product_faq['content']
                        );
                    }
                }
            }
        }

        if (!empty($faqs)) {
            $content .= '<div class="autozpro-faqs">';
            $content .= '<h2 class="autozprofaq-title">' . apply_filters('autozpro_product_faqs_title', 'Product Q&A') . '</h2>';
            $content .= '<div class="autozpro-faqs-accordion">';
            foreach ($faqs as $faq) {
                $content .= '<h4 class="autozpro-faq-title">' . $faq['title'] . '</h4>';
                $content .= '<div class="autozpro-faq wpcpf-faq-' . esc_attr($faq['type']) . '">';
                $content .= '<div class="autozpro-faq-content">' . $faq['content'] . '</div>';
                $content .= '</div><!-- /autozpro-faq -->';
            }
            $content .= '</div><!-- /autozpro-faqs-accordion -->';

            $content .= '</div><!-- /wpcpf-faqs -->';
        }

        return apply_filters('autozpro_product_faqs', $content, $product_id);
    }

    function autozpro_tab_content() {
        global $product;

        if ($product) {
            $product_id = $product->get_id();

            if ($product_id) {
                echo autozpro_product_faqs($product_id);
            }
        }

    }
}


if (!function_exists('autozpro_shortcode_woobt')) {
    function autozpro_shortcode_woobt() {
        if (function_exists('woobt_init')) {
            $position = apply_filters('woobt_position', get_option('_woobt_position', apply_filters('woobt_default_position', 'before')));
            if ($position == 'none') {
                echo do_shortcode('[woobt]');
            }
        }
    }
}

function matico_wc_track_product_view() {
    if (!is_singular('product') || is_active_widget(false, false, 'woocommerce_recently_viewed_products', true)) {
        return;
    }

    global $post;

    if (empty($_COOKIE['woocommerce_recently_viewed'])) { // @codingStandardsIgnoreLine.
        $viewed_products = array();
    } else {
        $viewed_products = wp_parse_id_list((array)explode('|', wp_unslash($_COOKIE['woocommerce_recently_viewed']))); // @codingStandardsIgnoreLine.
    }

    // Unset if already in viewed products list.
    $keys = array_flip($viewed_products);

    if (isset($keys[$post->ID])) {
        unset($viewed_products[$keys[$post->ID]]);
    }

    $viewed_products[] = $post->ID;

    if (count($viewed_products) > 15) {
        array_shift($viewed_products);
    }

    // Store for session only.
    wc_setcookie('woocommerce_recently_viewed', implode('|', $viewed_products));
}

add_action('template_redirect', 'matico_wc_track_product_view', 20);