<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

class Autozpro_Elementor_Search extends Elementor\Widget_Base {
    public function get_name() {
        return 'autozpro-search';
    }

    public function get_title() {
        return esc_html__('Autozpro Search Form', 'autozpro');
    }

    public function get_icon() {
        return 'eicon-site-search';
    }

    public function get_categories() {
        return array('autozpro-addons');
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
            ]
        );

        $this->add_control(
            'hide_search',
            [
                'label'        => esc_html__('Hide filter categories', 'autozpro'),
                'type'         => Controls_Manager::SWITCHER,
                'condition'    => [
                    'layout_style' => 'layout-1',
                ],
                'prefix_class' => 'search-form-hide-search-',
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
                'label'     => esc_html__('Border Focus', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color_focus',
            [
                'label'     => esc_html__('Background Focus', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_form',
            [
                'label'     => esc_html__('Background input', 'autozpro'),
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
        $settings = $this->get_settings_for_display();

        if ($settings['layout_style'] === 'layout-1'):{
            if(autozpro_is_woocommerce_activated()){
                autozpro_product_search();
            }else{
                ?>
                <div class="site-search widget_search">
                    <?php get_search_form(); ?>
                </div>
                <?php
            }

        }
        endif;

        if ($settings['layout_style'] === 'layout-2'):{
            wp_enqueue_script('autozpro-search-popup');
            add_action('wp_footer', 'autozpro_header_search_popup', 1);
            ?>
            <div class="site-header-search">
                <a href="#" class="button-search-popup">
                    <i class="autozpro-icon-search"></i>
                    <span class="content"><?php echo esc_html__('Search', 'autozpro'); ?></span>
                </a>
            </div>
            <?php
        }
        endif;
    }
}

$widgets_manager->register(new Autozpro_Elementor_Search());
