<?php

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!autozpro_is_woocommerce_activated()) {
    return;
}

class Autozpro_Elementor_Widget_Products_Related extends Elementor\Widget_Base {

    public function get_categories() {
        return array('autozpro-addons');
    }

    public function get_name() {
        return 'autozpro-product-related';
    }

    public function get_title() {
        return esc_html__('Product Related', 'autozpro');
    }

    public function get_icon() {
        return 'eicon-product-related';
    }

    public function get_keywords() {
        return ['woocommerce', 'shop', 'store', 'related', 'similar', 'product'];
    }

    public function get_script_depends() {
        return [
            'autozpro-elementor-product-related',
            'slick',
            'tooltipster'
        ];
    }

    /**
     *
     */
    protected function register_controls() {
        $this->start_controls_section(
            'section_related_products_content',
            [
                'label' => esc_html__('Related Products', 'autozpro'),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => esc_html__('Products Per Page', 'autozpro'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 4,
                'range'   => [
                    'px' => [
                        'max' => 20,
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => esc_html__('Columns', 'autozpro'),
                'type'           => Controls_Manager::NUMBER,
                'min'            => 1,
                'max'            => 12,
                'default'        => '4',
                'tablet_default' => '3',
                'mobile_default' => '2',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => esc_html__('Order By', 'autozpro'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'       => esc_html__('Date', 'autozpro'),
                    'title'      => esc_html__('Title', 'autozpro'),
                    'price'      => esc_html__('Price', 'autozpro'),
                    'popularity' => esc_html__('Popularity', 'autozpro'),
                    'rating'     => esc_html__('Rating', 'autozpro'),
                    'rand'       => esc_html__('Random', 'autozpro'),
                    'menu_order' => esc_html__('Menu Order', 'autozpro'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label'   => esc_html__('Order', 'autozpro'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__('ASC', 'autozpro'),
                    'desc' => esc_html__('DESC', 'autozpro'),
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_heading_style',
            [
                'label' => esc_html__('Heading', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_heading',
            [
                'label'        => esc_html__('Heading', 'autozpro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_off'    => esc_html__('Hide', 'autozpro'),
                'label_on'     => esc_html__('Show', 'autozpro'),
                'default'      => 'yes',
                'return_value' => 'yes',
                'prefix_class' => 'show-heading-',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'global'    => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '.woocommerce {{WRAPPER}}.elementor-wc-products .products > h2' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_heading!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'heading_typography',
                'global'    => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'selector'  => '.woocommerce {{WRAPPER}}.elementor-wc-products .products > h2',
                'condition' => [
                    'show_heading!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'heading_text_align',
            [
                'label'     => esc_html__('Text Align', 'autozpro'),
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
                    '.woocommerce {{WRAPPER}}.elementor-wc-products .products > h2' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    'show_heading!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'heading_spacing',
            [
                'label'      => esc_html__('Spacing', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors'  => [
                    '.woocommerce {{WRAPPER}}.elementor-wc-products .products > h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'  => [
                    'show_heading!' => '',
                ],
            ]
        );
        $this->add_control(
            'mobile_switcher',
            [
                'label'        => esc_html__('Mobile Switcher', 'autozpro'),
                'type'         => Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'prefix_class' => 'mobile-switcher-style-product-',
            ]
        );

        $this->end_controls_section();
        // Carousel Option
        $this->add_control_carousel();

    }

    protected function render() {
        global $product;

        $product = wc_get_product();

        if (!$product) {
            return;
        }

        $settings = $this->get_settings_for_display();

        $args              = [
            'posts_per_page' => 4,
            'columns'        => $settings['enable_carousel'] === 'yes' ? 1 : $settings['columns'],
            'orderby'        => $settings['orderby'],
            'order'          => $settings['order'],
        ];
        $class             = 'woocommerce';
        $carousel_settings = '';
        if (!empty($settings['posts_per_page'])) {
            $args['posts_per_page'] = $settings['posts_per_page'];
        }
        if ($settings['enable_carousel'] === 'yes') {
            $class             .= ' woocommerce-carousel';
            $carousel_settings = json_encode(wp_slash($this->get_carousel_settings()));
            wc_set_loop_prop('columns', 1);
        } else {

            if (!empty($settings['columns'])) {
                $args['columns'] = $settings['columns'];
                wc_set_loop_prop('columns', $settings['columns']);
            }
            if (!empty($settings['columns_widescreen'])) {
                $class .= ' columns-widescreen-' . $settings['columns_widescreen'];
            }

            if (!empty($settings['columns_laptop'])) {
                $class .= ' columns-laptop-' . $settings['columns_laptop'];
            }

            if (!empty($settings['columns_tablet_extra'])) {
                $class .= ' columns-tablet-extra-' . $settings['columns_tablet_extra'];
            }

            if (!empty($settings['columns_tablet'])) {
                $class .= ' columns-tablet-' . $settings['columns_tablet'];
            } else {
                $class .= ' columns-tablet-2';
            }

            if (!empty($settings['columns_mobile_extra'])) {
                $class .= ' columns-mobile-extra-' . $settings['columns_mobile_extra'];
            }

            if (!empty($settings['columns_mobile'])) {
                $class .= ' columns-mobile-' . $settings['columns_mobile'];
            } else {
                $class .= ' columns-mobile-1';
            }
        }

        // Get visible related products then sort them at random.
        $args['related_products'] = array_filter(array_map('wc_get_product', wc_get_related_products($product->get_id(), $args['posts_per_page'], $product->get_upsell_ids())), 'wc_products_array_filter_visible');

        // Handle orderby.
        $args['related_products'] = wc_products_array_orderby($args['related_products'], $args['orderby'], $args['order']);

        ob_start();
        ?>
        <div id="chungpham" class="<?php echo esc_attr($class); ?>" data-settings="<?php echo esc_attr($carousel_settings) ?>">
            <?php wc_get_template('single-product/related.php', $args); ?>
        </div>
        <?php
        $related_products_html = ob_get_clean();

        if ($related_products_html) {
            $related_products_html = str_replace('<ul class="products', '<ul class="products elementor-grid ', $related_products_html);

            echo wp_kses_post($related_products_html);
        }

    }

    protected function add_control_carousel($condition = array()) {
        $this->start_controls_section(
            'section_carousel_options',
            [
                'label'     => esc_html__('Carousel Options', 'autozpro'),
                'type'      => Controls_Manager::SECTION,
                'condition' => $condition,
            ]
        );

        $this->add_control(
            'enable_carousel',
            [
                'label' => esc_html__('Enable', 'autozpro'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label'     => esc_html__('Navigation', 'autozpro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'dots',
                'options'   => [
                    'both'   => esc_html__('Arrows and Dots', 'autozpro'),
                    'arrows' => esc_html__('Arrows', 'autozpro'),
                    'dots'   => esc_html__('Dots', 'autozpro'),
                    'none'   => esc_html__('None', 'autozpro'),
                ],
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label'     => esc_html__('Pause on Hover', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'     => esc_html__('Autoplay', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label'     => esc_html__('Autoplay Speed', 'autozpro'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 5000,
                'condition' => [
                    'autoplay'        => 'yes',
                    'enable_carousel' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
                ],
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label'     => esc_html__('Infinite Loop', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => '',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'style_layout_carousel',
            [
                'label'        => esc_html__('Layout Carousel', 'autozpro'),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    '1' => esc_html__('Layout 1', 'autozpro'),
                    '2' => esc_html__('Layout 2', 'autozpro'),
                ],
                'condition'    => [
                    'enable_carousel' => 'yes'
                ],
                'default'      => '1',
                'prefix_class' => 'layout-carousel-'
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_arrows',
            [
                'label'      => esc_html__('Carousel Arrows', 'autozpro'),
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => 'enable_carousel',
                            'operator' => '==',
                            'value'    => 'yes',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'none',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'dots',
                        ],
                    ],
                ],
            ]
        );


        //Style arrow

        $this->add_control(
            'hover_style_arrow',
            [
                'label'        => esc_html__('Hover Arrow', 'autozpro'),
                'type'         => Controls_Manager::SWITCHER,
                'prefix_class' => 'style-hover-arrow-carousel-',
            ]
        );

        $this->add_control(
            'style_arrow',
            [
                'label'        => esc_html__('Style Arrow', 'autozpro'),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    'style-1' => esc_html__('Style 1', 'autozpro'),
                    'style-2' => esc_html__('Style 2', 'autozpro'),
                    'style-3' => esc_html__('Style 3', 'autozpro'),
                ],
                'default'      => 'style-1',
                'prefix_class' => 'arrow-'
            ]
        );

        //add icon next size
        $this->add_responsive_control(
            'icon_size',
            [
                'label'     => esc_html__('Size', 'autozpro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-arrow:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'carousel_width',
            [
                'label'          => esc_html__('Width', 'autozpro'),
                'type'           => Controls_Manager::SLIDER,
                'default'        => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units'     => ['%', 'px', 'vw'],
                'range'          => [
                    '%'  => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'      => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'carousel_height',
            [
                'label'          => esc_html__('Height', 'autozpro'),
                'type'           => Controls_Manager::SLIDER,
                'default'        => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units'     => ['%', 'px', 'vw'],
                'range'          => [
                    '%'  => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'      => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        //add icon next color
        $this->add_control(
            'color_button',
            [
                'label' => esc_html__('Color', 'autozpro'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->start_controls_tabs('tabs_carousel_arrow_style');

        $this->start_controls_tab(
            'tab_carousel_arrow_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'carousel_arrow_color_icon',
            [
                'label'     => esc_html__('Color icon', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_border',
            [
                'label'     => esc_html__('Color Border', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_background',
            [
                'label'     => esc_html__('Color background', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_arrow_hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'carousel_arrow_color_icon_hover',
            [
                'label'     => esc_html__('Color icon', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_border_hover',
            [
                'label'     => esc_html__('Color Border', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_background_hover',
            [
                'label'     => esc_html__('Color background', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'next_heading',
            [
                'label' => esc_html__('Next button', 'autozpro'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'next_vertical',
            [
                'label'       => esc_html__('Next Vertical', 'autozpro'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'top'    => [
                        'title' => esc_html__('Top', 'autozpro'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'autozpro'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ]
            ]
        );

        $this->add_responsive_control(
            'next_vertical_value',
            [
                'type'       => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                    '%'  => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-next' => 'top: unset; bottom: unset; {{next_vertical.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );
        $this->add_control(
            'next_horizontal',
            [
                'label'       => esc_html__('Next Horizontal', 'autozpro'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'left'  => [
                        'title' => esc_html__('Left', 'autozpro'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'autozpro'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'defautl'     => 'right'
            ]
        );
        $this->add_responsive_control(
            'next_horizontal_value',
            [
                'type'       => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', 'em', '%'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                    '%'  => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => -45,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-next' => 'left: unset; right: unset;{{next_horizontal.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );


        $this->add_control(
            'prev_heading',
            [
                'label'     => esc_html__('Prev button', 'autozpro'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'prev_vertical',
            [
                'label'       => esc_html__('Prev Vertical', 'autozpro'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'top'    => [
                        'title' => esc_html__('Top', 'autozpro'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'autozpro'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ]
            ]
        );

        $this->add_responsive_control(
            'prev_vertical_value',
            [
                'type'       => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                    '%'  => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-prev' => 'top: unset; bottom: unset; {{prev_vertical.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );
        $this->add_control(
            'prev_horizontal',
            [
                'label'       => esc_html__('Prev Horizontal', 'autozpro'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'left'  => [
                        'title' => esc_html__('Left', 'autozpro'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'autozpro'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'defautl'     => 'left'
            ]
        );
        $this->add_responsive_control(
            'prev_horizontal_value',
            [
                'type'       => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', 'em', '%'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                    '%'  => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => -45,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-prev' => 'left: unset; right: unset; {{prev_horizontal.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_dots',
            [
                'label'      => esc_html__('Carousel Dots', 'autozpro'),
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => 'enable_carousel',
                            'operator' => '==',
                            'value'    => 'yes',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'none',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'arrows',
                        ],
                    ],
                ],
            ]
        );

        $this->start_controls_tabs('tabs_carousel_dots_style');

        $this->start_controls_tab(
            'tab_carousel_dots_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'carousel_dots_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity',
            [
                'label'     => esc_html__('Opacity', 'autozpro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_dots_hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'carousel_dots_color_hover',
            [
                'label'     => esc_html__('Color Hover', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-dots li button:focus:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity_hover',
            [
                'label'     => esc_html__('Opacity', 'autozpro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'opacity: {{SIZE}};',
                    '{{WRAPPER}} .slick-dots li button:focus:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_dots_activate',
            [
                'label' => esc_html__('Activate', 'autozpro'),
            ]
        );

        $this->add_control(
            'carousel_dots_color_activate',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity_activate',
            [
                'label'     => esc_html__('Opacity', 'autozpro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'dots_vertical_value',
            [
                'label'      => esc_html__('Spacing', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                    '%'  => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => '',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-dots'              => 'bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.dots-style-2 .slick-dots' => 'margin-top: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'Alignment_text',
            [
                'label'     => esc_html__('Alignment text', 'autozpro'),
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();
    }

    protected function get_carousel_settings() {
        $settings    = $this->get_settings_for_display();
        $breakpoints = \Elementor\Plugin::$instance->breakpoints->get_breakpoints();
        $tablet      = isset($settings['column_tablet']) ? $settings['column_tablet'] : 2;
        return array(
            'navigation'              => $settings['navigation'],
            'autoplayHoverPause'      => $settings['pause_on_hover'] === 'yes' ? true : false,
            'autoplay'                => $settings['autoplay'] === 'yes' ? true : false,
            'autoplayTimeout'         => empty($settings['autoplay_speed']) ? 5000 : $settings['autoplay_speed'],
            'layout_carousel'         => $settings['mobile_switcher'] === 'yes' ? true : false,
            'items'                   => $settings['columns'],
            'items_laptop'            => isset($settings['columns_laptop']) ? $settings['columns_laptop'] : $settings['columns'],
            'items_tablet_extra'      => isset($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $settings['columns'],
            'items_tablet'            => $tablet,
            'items_mobile_extra'      => isset($settings['columns_mobile_extra']) ? $settings['columns_mobile_extra'] : $tablet,
            'items_mobile'            => isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1,
            'loop'                    => $settings['infinite'] === 'yes' ? true : false,
            'breakpoint_laptop'       => $breakpoints['laptop']->get_value(),
            'breakpoint_tablet_extra' => $breakpoints['tablet_extra']->get_value(),
            'breakpoint_tablet'       => $breakpoints['tablet']->get_value(),
            'breakpoint_mobile_extra' => $breakpoints['mobile_extra']->get_value(),
            'breakpoint_mobile'       => $breakpoints['mobile']->get_value(),
        );
    }

}

$widgets_manager->register(new Autozpro_Elementor_Widget_Products_Related());
