<?php
/**
 * Product Autoparts Widget
 *
 * @author   WPOpal
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  2.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product categories widget class.
 *
 * @extends WC_Widget
 */
class Autozpro_Widget_Autoparts_Search extends WC_Widget {

    /**
     * Category ancestors.
     *
     * @var array
     */
    public $brand_ancestors;

    /**
     * Current Category.
     *
     * @var bool
     */
    public $current_brand;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'woocommerce widget_autoparts_search';
        $this->widget_description = esc_html__('Display a form to filter products in your store by Attributes', 'autozpro');
        $this->widget_id          = 'woocommerce_autoparts_search';
        $this->widget_name        = esc_html__('Autoparts Search', 'autozpro');

        parent::__construct();
    }

    /**
     * Output widget.
     *
     * @param array $args Widget arguments.
     * @param array $instance Widget instance.
     * @see WP_Widget
     */
    public function widget($args, $instance) {
        if (!autozpro_is_elementor_activated() || autozpro_get_theme_option('shop_search_position') != 'sidebar') {
            return;
        }

        $slug = autozpro_get_theme_option('shop_search_compatibility');

        $queried_post = get_page_by_path($slug, OBJECT, 'elementor_library');

        if (isset($queried_post->ID)) {
            echo Elementor\Plugin::instance()->frontend->get_builder_content($queried_post->ID);
        }

    }
}

add_action('widgets_init', function () {
    register_widget('Autozpro_Widget_Autoparts_Search');
});