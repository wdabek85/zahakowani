<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!autozpro_is_woocommerce_activated()) {
    return;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Autozpro_Elementor_Products_Tabs extends Elementor\Widget_Base {

    public function get_categories() {
        return array('autozpro-addons');
    }

    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     * @return string Widget name.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'autozpro-products-tabs';
    }

    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     * @return string Widget title.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__('Products Tabs', 'autozpro');
    }

    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     * @return string Widget icon.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-tabs';
    }


    public function get_script_depends() {
        return ['autozpro-elementor-product-tab', 'slick'];
    }

    public function on_export($element) {
        unset($element['settings']['categories']);
        unset($element['settings']['tag']);

        return $element;
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function register_controls() {

        $this->start_controls_section(
            'section_tabs',
            [
                'label' => esc_html__('Tabs', 'autozpro'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'tab_title',
            [
                'label'       => esc_html__('Tab Title', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('#Product Tab', 'autozpro'),
                'placeholder' => esc_html__('Product Tab Title', 'autozpro'),
            ]
        );

        $repeater->add_control(
            'limit',
            [
                'label'   => esc_html__('Posts Per Page', 'autozpro'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $repeater->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'autozpro'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $repeater->add_control(
            'orderby',
            [
                'label'   => esc_html__('Order By', 'autozpro'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'       => esc_html__('Date', 'autozpro'),
                    'id'         => esc_html__('Post ID', 'autozpro'),
                    'menu_order' => esc_html__('Menu Order', 'autozpro'),
                    'popularity' => esc_html__('Number of purchases', 'autozpro'),
                    'rating'     => esc_html__('Average Product Rating', 'autozpro'),
                    'title'      => esc_html__('Product Title', 'autozpro'),
                    'rand'       => esc_html__('Random', 'autozpro'),
                ],
            ]
        );

        $repeater->add_control(
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

        $repeater->add_control(
            'categories',
            [
                'label'       => esc_html__('Categories', 'autozpro'),
                'type'        => Controls_Manager::SELECT2,
                'label_block' => true,
                'options'     => $this->get_product_categories(),
                'multiple'    => true,
            ]
        );

        $repeater->add_control(
            'cat_operator',
            [
                'label'     => esc_html__('Category Operator', 'autozpro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'IN',
                'options'   => [
                    'AND'    => esc_html__('AND', 'autozpro'),
                    'IN'     => esc_html__('IN', 'autozpro'),
                    'NOT IN' => esc_html__('NOT IN', 'autozpro'),
                ],
                'condition' => [
                    'categories!' => ''
                ],
            ]
        );

        $repeater->add_control(
            'tag',
            [
                'label'       => esc_html__('Tags', 'autozpro'),
                'type'        => Controls_Manager::SELECT2,
                'label_block' => true,
                'options'     => $this->get_product_tags(),
                'multiple'    => true,
            ]
        );

        $repeater->add_control(
            'tag_operator',
            [
                'label'     => esc_html__('Tag Operator', 'autozpro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'IN',
                'options'   => [
                    'AND'    => esc_html__('AND', 'autozpro'),
                    'IN'     => esc_html__('IN', 'autozpro'),
                    'NOT IN' => esc_html__('NOT IN', 'autozpro'),
                ],
                'condition' => [
                    'tag!' => ''
                ],
            ]
        );

        $repeater->add_control(
            'product_type',
            [
                'label'   => esc_html__('Product Type', 'autozpro'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'newest',
                'options' => [
                    'newest'       => esc_html__('Newest Products', 'autozpro'),
                    'on_sale'      => esc_html__('On Sale Products', 'autozpro'),
                    'best_selling' => esc_html__('Best Selling', 'autozpro'),
                    'top_rated'    => esc_html__('Top Rated', 'autozpro'),
                    'featured'     => esc_html__('Featured Product', 'autozpro'),
                ],
            ]
        );

        $repeater->add_control(
            'product_layout',
            [
                'label'   => esc_html__('Product Layout', 'autozpro'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'autozpro'),
                    'list' => esc_html__('List', 'autozpro'),
                ],
            ]
        );

        $repeater->add_control(
            'style_block',
            [
                'label'     => esc_html__('Block Style', 'autozpro'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => '',
                'options'   => [
                    ''       => esc_html__('Style 1', 'autozpro'),
                    'style2' => esc_html__('Style 2', 'autozpro'),
                ],
                'condition' => [
                    'product_layout' => 'grid'
                ]
            ]
        );

        $repeater->add_control(
            'list_layout',
            [
                'label'     => esc_html__('List Layout', 'autozpro'),
                'type'      => Controls_Manager::SELECT,
                'default'   => '1',
                'options'   => [
                    '1' => esc_html__('Style 1', 'autozpro'),
                ],
                'condition' => [
                    'product_layout' => 'list'
                ]
            ]
        );

        $repeater->start_controls_tabs('tabs_color_title_style');

        $repeater->start_controls_tab(
            'tab_color_title_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $repeater->add_control(
            'tab_border_color',
            [
                'label'     => __('Boder Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.elementor-tab-title' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'tab_background_color',
            [
                'label'     => __('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.elementor-tab-title' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tab_color_title_hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $repeater->add_control(
            'tab_border_color_hover',
            [
                'label'     => __('Boder Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.elementor-tab-title:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'tab_background_color_hover',
            [
                'label'     => __('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.elementor-tab-title:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tab_color_title_active',
            [
                'label' => esc_html__('Active', 'autozpro'),
            ]
        );

        $repeater->add_control(
            'tab_border_color_active',
            [
                'label'     => __('Boder Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.elementor-tab-title.elementor-active' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'tab_background_color_active',
            [
                'label'     => __('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.elementor-tab-title.elementor-active' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'tabs',
            [
                'label'       => '',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'tab_title' => esc_html__('#Product Tab 1', 'autozpro'),
                    ],
                    [
                        'tab_title' => esc_html__('#Product Tab 2', 'autozpro'),
                    ]
                ],
                'title_field' => '{{{ tab_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_product_layout',
            [
                'label' => esc_html__('Layout', 'autozpro'),
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label'          => esc_html__('columns', 'autozpro'),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options'        => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6],
            ]
        );

        $this->add_responsive_control(
            'product_gutter',
            [
                'label'      => esc_html__('Gutter', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} ul.products li.product' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-right: calc({{SIZE}}{{UNIT}} / 2); margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ul.products'            => 'margin-left: calc({{SIZE}}{{UNIT}} / -2); margin-right: calc({{SIZE}}{{UNIT}} / -2);',
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

        $this->start_controls_section(
            'section_product_tab_title',
            [
                'label' => esc_html__('Header', 'autozpro'),
            ]
        );

        $this->add_control(
            'product_tab_title',
            [
                'label' => esc_html__('Title', 'autozpro'),
                'type'  => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'product_tab_button',
            [
                'label' => esc_html__('Button Text', 'autozpro'),
                'type'  => \Elementor\Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'product_tab_button_link',
            [
                'label'       => esc_html__('Button Link', 'autozpro'),
                'placeholder' => esc_html__('https://your-link.com', 'autozpro'),
                'type'        => Controls_Manager::URL,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_tab_header_style',
            [
                'label' => esc_html__('Header', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'align_items',
            [
                'label'       => esc_html__('Alignment', 'autozpro'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'flex-start'    => [
                        'title' => esc_html__('Left', 'autozpro'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center'        => [
                        'title' => esc_html__('Center', 'autozpro'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'flex-end'      => [
                        'title' => esc_html__('Right', 'autozpro'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                    'space-between' => [
                        'title' => esc_html__('Justified', 'autozpro'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'default'     => '',
                'selectors'   => [
                    '{{WRAPPER}} .elementor-tabs-header' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'vertical_align',
            [
                'label'     => esc_html__('Vertical Align', 'autozpro'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start' => [
                        'title' => esc_html__('Start', 'autozpro'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'center'     => [
                        'title' => esc_html__('Center', 'autozpro'),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'flex-end'   => [
                        'title' => esc_html__('End', 'autozpro'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-tabs-header' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_tab_header',
            [
                'label'     => esc_html__('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tabs-header' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'border_tabs_header',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .elementor-tabs-header',
            ]
        );

        $this->add_responsive_control(
            'tab_header_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-tabs-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Tabs title', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'tab_title_layout_style',
            [
                'label'        => esc_html__('Layout', 'autozpro'),
                'type'         => Controls_Manager::SELECT,
                'default'      => '1',
                'options'      => [
                    '1' => esc_html__('Style 1', 'autozpro'),
                    '2' => esc_html__('Style 2', 'autozpro'),
                    '3' => esc_html__('Style 3', 'autozpro'),
                    '4' => esc_html__('Style 4', 'autozpro'),
                ],
                'prefix_class' => 'title-style-',
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tab_typography',
                'selector' => '{{WRAPPER}} .elementor-tab-title',
            ]
        );

        $this->add_responsive_control(
            'header_margin',
            [
                'label'      => esc_html__('Spacing Between Item', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-tab-title' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 ); margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_title_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_title_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-tab-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_background_color',
            [
                'label'     => esc_html__('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'title_divider_color',
            [
                'label'     => esc_html__('Divider Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title:after' => 'background-color: {{VALUE}}'
                ],
            ]
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_background_hover_color',
            [
                'label'     => esc_html__('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title:hover' => 'background-color: {{VALUE}}'
                ],
            ]
        );

        $this->add_control(
            'title_hover_divider_color',
            [
                'label'     => esc_html__('Divider Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title:hover:after' => 'background-color: {{VALUE}}'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_active',
            [
                'label' => esc_html__('Active', 'autozpro'),
            ]
        );

        $this->add_control(
            'title_active_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title.elementor-active' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'title_background_active_color',
            [
                'label'     => esc_html__('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title.elementor-active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_active_divider_color',
            [
                'label'     => esc_html__('Divider Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-tab-title.elementor-active:after' => 'background-color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'divider_height',
            [
                'label'      => esc_html__('Divider Height', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-tab-title:after' => 'height: calc( {{SIZE}}{{UNIT}}/2 );',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__('Content', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'tab_content_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tab_content_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-tab-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_tab_title_style',
            [
                'label' => esc_html__('Title', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tab_header_title',
                'selector' => '{{WRAPPER}} .product-tab-title',
            ]
        );

        $this->add_control(
            'tab_header_title_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-tab-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_header_title_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .product-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tab_header_title_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .product-tab-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_tab_button_style',
            [
                'label' => esc_html__('Button', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'tab_header_button',
                'selector' => '{{WRAPPER}} .product-tab-button',
            ]
        );

        $this->add_control(
            'tab_header_button_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-tab-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'tab_header_button_color_hover',
            [
                'label'     => esc_html__('Color Hover', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-tab-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_header_button_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .product-tab-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tab_header_button_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .product-tab-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();

        $this->add_control_carousel();

    }

    /**
     * Render tabs widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render() {
        $tabs    = $this->get_settings_for_display('tabs');
        $setting = $this->get_settings_for_display();

        $id_int = substr($this->get_id_int(), 0, 3);

        $this->add_render_attribute('data-carousel', 'class', 'elementor-tabs-content-wrapper');

        if ($setting['enable_carousel']) {

            $carousel_settings = $this->get_carousel_settings($setting);
            $this->add_render_attribute('data-carousel', 'data-settings', wp_json_encode($carousel_settings));
        }

        ?>
        <div class="elementor-tabs" role="tablist">
            <div class="elementor-tabs-header">
                <?php
                if ($setting['product_tab_title'] != '') {
                    echo '<h3 class="product-tab-title">' . esc_html($setting['product_tab_title']) . '</h3>';
                }
                ?>
                <div class="elementor-tabs-wrapper">
                    <?php
                    foreach ($tabs as $index => $item) :
                        $tab_count = $index + 1;
                        $class_item = 'elementor-repeater-item-' . $item['_id'];
                        $class = ($index == 0) ? 'elementor-active' : '';

                        $tab_title_setting_key = $this->get_repeater_setting_key('tab_title', 'tabs', $index);

                        $this->add_render_attribute($tab_title_setting_key, [
                            'id'            => 'elementor-tab-title-' . $id_int . $tab_count,
                            'class'         => [
                                'elementor-tab-title',
                                'elementor-tab-desktop-title',
                                $class,
                                $class_item
                            ],
                            'data-tab'      => $tab_count,
                            'tabindex'      => $id_int . $tab_count,
                            'role'          => 'tab',
                            'aria-controls' => 'elementor-tab-content-' . $id_int . $tab_count,
                        ]);
                        ?>
                        <div <?php echo autozpro_elementor_get_render_attribute_string($tab_title_setting_key, $this); // WPCS: XSS ok.
                        ?>><?php echo esc_html($item['tab_title']); ?></div>
                    <?php endforeach; ?>

                </div>
                <?php
                if ($setting['product_tab_button'] != '') {
                    $button_html = '<span>' . $setting['product_tab_button'] . '</span><i class="autozpro-icon-right-arrow"></i>';
                    $this->add_render_attribute('link', 'class', 'product-tab-button');
                    if (!empty($setting['product_tab_button_link']['url'])) {
                        $this->add_link_attributes('link', $setting['product_tab_button_link']);
                    }

                    echo '<a ' . $this->get_render_attribute_string('link') . '>' . $button_html . '</a>';
                }
                ?>
            </div>
            <div <?php echo autozpro_elementor_get_render_attribute_string('data-carousel', $this); // WPCS: XSS ok.
            ?>>
                <?php
                foreach ($tabs as $index => $item) :
                    $tab_count = $index + 1;
                    $class_item = 'elementor-repeater-item-' . $item['_id'];
                    $class_content = ($index == 0) ? 'elementor-active' : '';

                    $tab_content_setting_key = $this->get_repeater_setting_key('tab_content', 'tabs', $index);

                    $this->add_render_attribute($tab_content_setting_key, [
                        'id'              => 'elementor-tab-content-' . $id_int . $tab_count,
                        'class'           => [
                            'elementor-tab-content',
                            'elementor-clearfix',
                            $class_content,
                            $class_item
                        ],
                        'data-tab'        => $tab_count,
                        'role'            => 'tabpanel',
                        'aria-labelledby' => 'elementor-tab-title-' . $id_int . $tab_count,
                    ]);

                    $this->add_inline_editing_attributes($tab_content_setting_key, 'advanced');
                    ?>
                    <div <?php echo autozpro_elementor_get_render_attribute_string($tab_content_setting_key, $this); // WPCS: XSS ok.
                    ?>>
                        <?php $this->woocommerce_default($item, $setting); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function woocommerce_default($settings, $global_setting) {
        $type = 'products';

        $class = '';

        if ($settings['product_layout'] == 'list') {
            $atts['style'] = 'list-' . $settings['list_layout'];
            $class              .= ' woocommerce-product-list-' . $settings['list_layout'];
        } else {
            if (isset($settings['style_block']) && $settings['style_block'] !== '') {
                $atts['style-grid'] = $settings['style_block'];
            }
        }

        if ($global_setting['enable_carousel']) {

            $atts['product_layout'] = 'carousel';
            $atts                   = [
                'limit'             => $settings['limit'],
                'orderby'           => $settings['orderby'],
                'order'             => $settings['order'],
                'carousel_settings' => '',
                'columns'           => 1,
                'product_layout'    => 'carousel'
            ];

        } else {
            $atts = [
                'limit'             => $settings['limit'],
                'orderby'           => $settings['orderby'],
                'order'             => $settings['order'],
                'carousel_settings' => '',
                'columns'           => $global_setting['column'],
                'product_layout'    => $settings['product_layout']
            ];

            if (!empty($global_setting['column_widescreen'])) {
                $class .= ' columns-widescreen-' . $global_setting['column_widescreen'];
            }

            if (!empty($global_setting['column_laptop'])) {
                $class .= ' columns-laptop-' . $global_setting['column_laptop'];
            }

            if (!empty($global_setting['column_tablet_extra'])) {
                $class .= ' columns-tablet-extra-' . $global_setting['column_tablet_extra'];
            }

            if (!empty($global_setting['column_tablet'])) {
                $class .= ' columns-tablet-' . $global_setting['column_tablet'];
            } else {
                $class .= ' columns-tablet-2';
            }

            if (!empty($global_setting['column_mobile_extra'])) {
                $class .= ' columns-mobile-extra-' . $global_setting['column_mobile_extra'];
            }

            if (!empty($global_setting['column_mobile'])) {
                $class .= ' columns-mobile-' . $global_setting['column_mobile'];
            } else {
                $class .= ' columns-mobile-1';
            }
        }

        $atts = $this->get_product_type($atts, $settings['product_type']);
        if (isset($atts['on_sale']) && wc_string_to_bool($atts['on_sale'])) {
            $type = 'sale_products';
        } elseif (isset($atts['best_selling']) && wc_string_to_bool($atts['best_selling'])) {
            $type = 'best_selling_products';
        } elseif (isset($atts['top_rated']) && wc_string_to_bool($atts['top_rated'])) {
            $type = 'top_rated_products';
        }

        if (!empty($settings['categories'])) {
            $atts['category']     = implode(',', $settings['categories']);
            $atts['cat_operator'] = $settings['cat_operator'];
        }

        if (!empty($settings['tag'])) {
            $atts['tag']          = implode(',', $settings['tag']);
            $atts['tag_operator'] = $settings['tag_operator'];
        }

        $atts['class'] = $class;

        echo (new WC_Shortcode_Products($atts, $type))->get_content(); // WPCS: XSS ok.
    }

    protected function get_product_type($atts, $product_type) {
        switch ($product_type) {
            case 'featured':
                $atts['visibility'] = "featured";
                break;

            case 'on_sale':
                $atts['on_sale'] = true;
                break;

            case 'best_selling':
                $atts['best_selling'] = true;
                break;

            case 'top_rated':
                $atts['top_rated'] = true;
                break;

            default:
                break;
        }

        return $atts;
    }

    protected function get_product_tags() {
        $tags    = get_terms(array(
                'taxonomy'   => 'product_tag',
                'hide_empty' => false,
            )
        );
        $results = array();
        if (!is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $results[$tag->slug] = $tag->name;
            }
        }

        return $results;
    }

    protected function get_product_categories() {
        $categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            )
        );
        $results    = array();
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }

        return $results;
    }

    protected function get_carousel_settings($settings) {
        $breakpoints = \Elementor\Plugin::$instance->breakpoints->get_breakpoints();

        $tablet = isset($settings['column_tablet']) ? $settings['column_tablet'] : 2;

        return array(
            'navigation'              => $settings['navigation'],
            'autoplayHoverPause'      => $settings['pause_on_hover'] === 'yes' ? true : false,
            'autoplay'                => $settings['autoplay'] === 'yes' ? true : false,
            'autoplayTimeout'         => empty($settings['autoplay_speed']) ? 5000 : $settings['autoplay_speed'],
            'layout_carousel'         => $settings['mobile_switcher'] === 'yes' ? true : false,
            'items'                   => $settings['column'],
            'items_laptop'            => isset($settings['column_laptop']) ? $settings['column_laptop'] : $settings['column'],
            'items_tablet_extra'      => isset($settings['column_tablet_extra']) ? $settings['column_tablet_extra'] : $settings['column'],
            'items_tablet'            => $tablet,
            'items_mobile_extra'      => isset($settings['column_mobile_extra']) ? $settings['column_mobile_extra'] : $tablet,
            'items_mobile'            => isset($settings['column_mobile']) ? $settings['column_mobile'] : 1,
            'loop'                    => $settings['infinite'] === 'yes' ? true : false,
            'breakpoint_laptop'       => $breakpoints['laptop']->get_value(),
            'breakpoint_tablet_extra' => $breakpoints['tablet_extra']->get_value(),
            'breakpoint_tablet'       => $breakpoints['tablet']->get_value(),
            'breakpoint_mobile_extra' => $breakpoints['mobile_extra']->get_value(),
            'breakpoint_mobile'       => $breakpoints['mobile']->get_value(),
        );
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
                            'value'    => 'both',
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
                    '{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
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
}

$widgets_manager->register(new Autozpro_Elementor_Products_Tabs());
