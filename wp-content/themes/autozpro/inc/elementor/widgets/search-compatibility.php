<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

class Autozpro_Elementor_Search_Compatibility extends Elementor\Widget_Base {
    public function get_name() {
        return 'autozpro-search-compatibility';
    }

    public function get_title() {
        return esc_html__('Autozpro Search Compatibility', 'autozpro');
    }

    public function get_icon() {
        return 'eicon-site-search';
    }

    public function get_categories() {
        return array('autozpro-addons');
    }
    public function get_script_depends() {
        return [
            'autozpro-elementor-search-compatibility',
            'jquery-elementor-select2',
        ];
    }
    public function get_style_depends() {
        return [
            'elementor-select2',
        ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'search-form-style',
            [
                'label' => esc_html__('Style', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'layout_style',
            [
                'label'   => esc_html__('Layout Style', 'autozpro'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'layout-1' => esc_html__('Layout 1', 'autozpro'),
                    'layout-2' => esc_html__('Layout 2', 'autozpro'),
                ],
                'default' => 'layout-1',
                'prefix_class' => 'search-compatibility-',
            ]
        );

        $this->add_responsive_control(
            'border_width',
            [
                'label'      => esc_html__('Border width', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} form input[type=search]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label'     => esc_html__('Border Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_color_focus',
            [
                'label'     => esc_html__('Border Color Focus', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_form',
            [
                'label'     => esc_html__('Background Form', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color_form',
            [
                'label'     => esc_html__('Color Icon', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .widget_product_search form::before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius_input',
            [
                'label'      => esc_html__('Border Radius Input', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .widget_product_search form input[type=search]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        echo Autozpro_Woocommerce_AutoParts::get_instance()->vehicle_select_shortcode();
    }

}

$widgets_manager->register(new Autozpro_Elementor_Search_Compatibility());
