<?php
// Button
use Elementor\Controls_Manager;

add_action( 'elementor/element/button/section_style/after_section_end', function ($element, $args ) {

    $element->update_control(
        'background_color',
        [
            'global' => [
                'default' => '',
            ],
			'selectors' => [
				'{{WRAPPER}} .elementor-button' => ' background-color: {{VALUE}};',
			],
        ]
    );
}, 10, 2 );

add_action('elementor/element/button/section_style/before_section_end', function ($element, $args) {
    $element->add_control(
        'icon_button_size',
        [
            'label' => esc_html__('Icon Size', 'autozpro'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 6,
                    'max' => 300,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-button .elementor-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'selected_icon[value]!' => '',
            ],
        ]
    );
    $element->add_control(
        'button_icon_color',
        [
            'label'     => esc_html__('Icon Color', 'autozpro'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
            ],
            'condition' => [
                'selected_icon[value]!' => '',
            ],
        ]
    );
}, 10, 2);

add_action('elementor/element/button/section_style/before_section_end', function ($element, $args) {
    $element->add_control(
        'button_style_theme',
        [
            'label'        => esc_html__('Style', 'autozpro'),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'prefix_class' => 'button-style-autozpro-',
        ]
    );

    $element->add_control(
        'button_background_color_1',
        [
            'label'     => esc_html__('Background Color', 'autozpro'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-button:before' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'button_style_theme' => 'yes',
            ],
        ]
    );

}, 10, 2);