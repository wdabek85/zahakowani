<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!autozpro_is_woocommerce_activated()) {
    return;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Autozpro_Elementor_Breadcrumb extends Elementor\Widget_Base {

    public function get_name() {
        return 'woocommerce-breadcrumb';
    }

    public function get_title() {
        return esc_html__('Autozpro WooCommerce Breadcrumbs', 'autozpro');
    }

    public function get_icon() {
        return 'eicon-product-breadcrumbs';
    }

    public function get_categories() {
        return ['autozpro-addons'];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_product_rating_style',
            [
                'label' => esc_html__('Style Breadcrumb', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'wc_style_warning',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'autozpro'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Text Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-breadcrumb' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'link_color',
            [
                'label'     => esc_html__('Link Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-breadcrumb > a:not(:hover)' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'link_color_hover',
            [
                'label'     => esc_html__('Link Hover', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-breadcrumb > a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'    => esc_html__('Typography Link', 'autozpro'),
                'name'     => 'text_link_typography',
                'selector' => '{{WRAPPER}} .woocommerce-breadcrumb a',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'    => esc_html__('Typography Text', 'autozpro'),
                'name'     => 'text_typography',
                'selector' => '{{WRAPPER}} .woocommerce-breadcrumb',
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label'     => esc_html__('Alignment', 'autozpro'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'autozpro'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'autozpro'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'autozpro'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-breadcrumb'   => 'text-align: {{VALUE}}',
                    '{{WRAPPER}} .autozpro-woocommerce-title' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_product_rating_style_title',
            [
                'label' => esc_html__('Style Title', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color_title',
            [
                'label'     => esc_html__('Title Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .autozpro-woocommerce-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .autozpro-woocommerce-title',
            ]
        );

        $this->add_control(
            'display_title',
            [
                'label'        => esc_html__('Hidden Title', 'autozpro'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'prefix_class' => 'hidden-autozpro-title-'
            ]
        );

        $this->add_control(
            'display_title_single',
            [
                'label'        => esc_html__('Hidden Title Single', 'autozpro'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'prefix_class' => 'hidden-autozpro-title-single-'
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .autozpro-woocommerce-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $args        = apply_filters(
            'woocommerce_breadcrumb_defaults',
            array(
                'delimiter'   => '<i class="autozpro-icon-angle-right"></i>',
                'wrap_before' => '<nav class="woocommerce-breadcrumb">',
                'wrap_after'  => '</nav>',
                'before'      => '',
                'after'       => '',
                'home'        => _x('Home ', 'breadcrumb', 'autozpro'),
            )
        );
        $breadcrumbs = new WC_Breadcrumb();
        if (!empty($args['home'])) {
            $breadcrumbs->add_crumb($args['home'], apply_filters('woocommerce_breadcrumb_home_url', home_url()));
        }
        $args['breadcrumb'] = $breadcrumbs->generate();

        /**
         * WooCommerce Breadcrumb hook
         *
         * @see WC_Structured_Data::generate_breadcrumblist_data() - 10
         */
        do_action('woocommerce_breadcrumb', $breadcrumbs, $args);

        wc_get_template('global/breadcrumb.php', $args);

        printf('<div class="autozpro-woocommerce-title">%s</div>', end($args['breadcrumb'])[0]);
    }

    public function render_plain_content() {
    }
}

$widgets_manager->register(new Autozpro_Elementor_Breadcrumb());
