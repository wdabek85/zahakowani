<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!autozpro_is_mailchimp_activated()) {
    return;
}

use Elementor\Group_Control_Border;
use Elementor\Controls_Manager;


class Autozpro_Elementor_Mailchimp extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'autozpro-mailchmip';
    }

    public function get_title()
    {
        return esc_html__('MailChimp Sign-Up Form', 'autozpro');
    }

    public function get_categories()
    {
        return array('autozpro-addons');
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_script_depends()
    {
        return ['magnific-popup'];
    }

    public function get_style_depends()
    {
        return ['magnific-popup'];
    }


    protected function register_controls()
    {

        $this->start_controls_section(
            'section_config',
            [
                'label' => esc_html__('Config', 'autozpro'),
            ]
        );

        $this->add_control(
            'mail_layout',
            [
                'label' => esc_html__('Layout', 'autozpro'),
                'type' => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => esc_html__('Layout 1', 'autozpro'),
                    '2' => esc_html__('Layout 2', 'autozpro'),
                    '3' => esc_html__('Layout 3', 'autozpro'),
                ],
                'prefix_class' => 'form-mailchimp-style-',
            ]
        );

        $this->end_controls_section();

        //INPUT
        $this->start_controls_section(
            'mailchimp_style_input',
            [
                'label' => esc_html__('Input', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs('tabs_input_style');

        $this->start_controls_tab(
            'tab_input_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'input_color',
            [
                'label' => esc_html__('Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-style .mc4wp-form .mc4wp-form-fields input[type="email"]' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'input_color_placeholder',
            [
                'label' => esc_html__('Color Placeholder', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-style .mc4wp-form .mc4wp-form-fields input[type="email"]::placeholder' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'input_background',
            [
                'label' => esc_html__('Background Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-style .mc4wp-form .mc4wp-form-fields input[type="email"]' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_color_border',
            [
                'label' => esc_html__('Color Border', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-style .mc4wp-form .mc4wp-form-fields input[type="email"]' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_input_focus',
            [
                'label' => esc_html__('Focus', 'autozpro'),
            ]
        );

        $this->add_control(
            'input_background_focus',
            [
                'label' => esc_html__('Background Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-style .mc4wp-form .mc4wp-form-fields input[type="email"]:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_color_focus',
            [
                'label' => esc_html__('Border Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-style .mc4wp-form .mc4wp-form-fields input[type="email"]:focus' => 'border-color: {{VALUE}}',
                ],
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(

            Group_Control_Border::get_type(),
            [
                'name' => 'border_input',
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .mc4wp-form-fields input[type="email"]',
                'separator' => 'before',
            ]
        );


        $this->add_responsive_control(
            'input_border_radius',
            [
                'label' => esc_html__('Border Radius', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields input[type="email"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'input_margin',
            [
                'label' => esc_html__('Margin', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields input[type="email"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]

        );

        $this->add_control(
            'input_padding',
            [
                'label' => esc_html__('Padding', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields input[type="email"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]

        );

        $this->end_controls_section();

        //Button
        $this->start_controls_section(
            'mailchip_style_button',
            [
                'label' => esc_html__('Button', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'button_bacground',
            [
                'label' => esc_html__('Background Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => esc_html__('Text Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:not(:hover)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Icon Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border',
            [
                'label' => esc_html__('Border Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'button_bacground_hover',
            [
                'label' => esc_html__('Background Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label' => esc_html__('Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_hover',
            [
                'label' => esc_html__('Border Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_icon_hover',
            [
                'label' => esc_html__('Icon Color hover', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:hover i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_icon_hover',
            [
                'label' => esc_html__('Icon Border Color hover', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:hover span.icon:before' => 'border-top-color: {{VALUE}}; border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]:hover span.icon:after' => 'border-top-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_button',
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mc4wp-form-fields button[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .form-style .mc4wp-form .mc4wp-form-fields button[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        echo '<div class="form-style">';
            mc4wp_show_form();
        echo '</div>';
    }
}

$widgets_manager->register(new Autozpro_Elementor_Mailchimp());
