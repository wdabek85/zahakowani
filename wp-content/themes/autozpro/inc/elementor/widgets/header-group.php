<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Autozpro_Elementor_Header_Group extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'autozpro-header-group';
    }

    public function get_title()
    {
        return esc_html__('Autozpro Header Group', 'autozpro');
    }

    public function get_icon()
    {
        return 'eicon-lock-user';
    }

    public function get_categories()
    {
        return array('autozpro-addons');
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'header_group_config',
            [
                'label' => esc_html__('Config', 'autozpro'),
            ]
        );

        $this->add_control(
            'show_search',
            [
                'label' => esc_html__('Show search', 'autozpro'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_account',
            [
                'label' => esc_html__('Show account', 'autozpro'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_wishlist',
            [
                'label' => esc_html__('Show wishlist', 'autozpro'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'show_cart',
            [
                'label' => esc_html__('Show cart', 'autozpro'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'header-group-style',
            [
                'label' => esc_html__('Icon', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-header-group-wrapper .header-group-action > div a:not(:hover) i:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color_hover',
            [
                'label' => esc_html__('Color Hover', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-header-group-wrapper .header-group-action > div a:hover i:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Font Size', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-header-group-wrapper .header-group-action > div a i:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'header-style-cart',
            [
                'label' => esc_html__('Content Cart', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_cart' => 'yes',
                ],

            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__('Title', 'autozpro'),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .site-header-cart .cart-text .title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__('Price', 'autozpro'),
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .site-header-cart .cart-text .amount',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .site-header-cart .cart-text .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => esc_html__('Price Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .site-header-cart .cart-text .amount' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_cart_space',
            [
                'label' => esc_html__('Spacing', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .site-header-cart .cart-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'elementor-header-group-wrapper');
        ?>
        <div <?php echo autozpro_elementor_get_render_attribute_string('wrapper', $this); ?>>
            <div class="header-group-action">

                <?php if ($settings['show_search'] === 'yes'):{
                    autozpro_header_search_button();
                }
                endif; ?>

                <?php if ($settings['show_account'] === 'yes'):{
                    autozpro_header_account();
                }
                endif; ?>

                <?php if ($settings['show_wishlist'] === 'yes' && autozpro_is_woocommerce_activated()):{
                    autozpro_header_wishlist();
                }
                endif; ?>

                <?php if ($settings['show_cart'] === 'yes' && autozpro_is_woocommerce_activated()):{
                    autozpro_header_cart();
                }
                endif; ?>

            </div>
        </div>
        <?php
    }
}

$widgets_manager->register(new Autozpro_Elementor_Header_Group());
