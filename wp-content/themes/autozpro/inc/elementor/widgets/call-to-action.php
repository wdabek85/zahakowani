<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Autozpro_Call_To_Action extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'autozpro-banner';
    }

    public function get_title()
    {
        return esc_html__('Autozpro Banner', 'autozpro');
    }

    public function get_icon()
    {
        return 'eicon-image-rollover';
    }

    public function get_categories()
    {
        return ['autozpro-addons'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Content', 'autozpro'),
            ]
        );

        $this->add_control(
            'bg_image',
            [
                'label' => esc_html__('Choose Background Image', 'autozpro'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'bg_image', // Actually its `image_size`
                'label' => esc_html__('Image Resolution', 'autozpro'),
                'default' => 'large',
                'condition' => [
                    'bg_image[id]!' => '',
                ],
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label' => esc_html__('Sub title', 'autozpro'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('This is the sub title', 'autozpro'),
                'placeholder' => esc_html__('Enter your sub title', 'autozpro'),
                'label_block' => true,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title & Description', 'autozpro'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('This is the heading', 'autozpro'),
                'placeholder' => esc_html__('Enter your title', 'autozpro'),
                'label_block' => true,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'autozpro'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'autozpro'),
                'placeholder' => esc_html__('Enter your description', 'autozpro'),
                'separator' => 'none',
                'rows' => 5,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'autozpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                ],
                'default' => 'h3',
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->add_control(
            'button',
            [
                'label' => esc_html__('Button Text', 'autozpro'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('Click Here', 'autozpro'),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__('Link', 'autozpro'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '#',
                ],
                'placeholder' => esc_html__('https://your-link.com', 'autozpro'),

            ]
        );

        $this->add_control(
            'link_click',
            [
                'label' => esc_html__('Apply Link On', 'autozpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'box' => esc_html__('Whole Box', 'autozpro'),
                    'button' => esc_html__('Button Only', 'autozpro'),
                ],
                'default' => 'button',
                'separator' => 'none',
                'condition' => [
                    'link[url]!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'box_style',
            [
                'label' => esc_html__('Box', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content-stretch',
            [
                'label' => esc_html__('Stretch', 'autozpro'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'content-stretch-'
            ]
        );

        $this->add_responsive_control(
            'min-height',
            [
                'label' => esc_html__('Height', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'vh'],
                'condition' => [
                    'content-stretch' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__content' => 'min-height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .skeleton-item' => 'min-height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .skeleton-item:before' => 'padding-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'alignment',
            [
                'label' => esc_html__('Alignment', 'autozpro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'autozpro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'autozpro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'autozpro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__content' => 'text-align: {{VALUE}}',
                ],
                'prefix_class' => 'box-align-'
            ]
        );

        $this->add_control(
            'vertical_position',
            [
                'label' => esc_html__('Vertical Position', 'autozpro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top', 'autozpro'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => esc_html__('Middle', 'autozpro'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'autozpro'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'prefix_class' => 'elementor-cta--valign-',
                'separator' => 'none',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'box_border_radius',
            [
                'label' => esc_html__('Border Radius', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__bg-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__('Content', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'autozpro' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%'],

                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__content_inner' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'Horizontal_align',
            [
                'label' => esc_html__( 'Horizontal Align', 'autozpro' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__( 'Left', 'autozpro' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'autozpro' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__( 'Right', 'autozpro' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__content' => 'justify-content: {{VALUE}}',
                ],
            ]
        );


        $this->add_control(
            'heading_style_title',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Title', 'autozpro'),
                'separator' => 'before',
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .elementor-cta__title',
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-cta__title',
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->add_control(
            'title_background',
            [
                'label' => esc_html__('Background Title', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__title > span:before' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .elementor-cta__title > span:after' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_background_effects',
            [
                'label' => esc_html__('Effects Background', 'autozpro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'autozpro'),
                    'before' => esc_html__('Before', 'autozpro'),
                    'after' => esc_html__('After', 'autozpro'),
                    'both' => esc_html__('Both', 'autozpro'),
                ],
                'selectors' => [
                    '{{WRAPPER}}.title-background-effects-before .elementor-content-item.elementor-cta__title > span:before' => 'content: "";',
                    '{{WRAPPER}}.title-background-effects-after .elementor-content-item.elementor-cta__title > span:after' => 'content: "";',
                    '{{WRAPPER}}.title-background-effects-both .elementor-content-item.elementor-cta__title > span:after, {{WRAPPER}}.title-background-effects-both .elementor-content-item.elementor-cta__title > span:before' => 'content: "";',
                ],
                'prefix_class' => 'title-background-effects-'
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => esc_html__('Spacing', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-content-item.elementor-cta__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title!' => '',
                ],
            ]
        );


        $this->add_control(
            'heading_style_subtitle',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Subtitle', 'autozpro'),
                'separator' => 'before',
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'selector' => '{{WRAPPER}} .elementor-cta__subtitle',
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'subtitle_shadow',
                'selector' => '{{WRAPPER}} .elementor-cta__subtitle',
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'subtitle_spacing',
            [
                'label' => esc_html__('Spacing', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-content-item.elementor-cta__subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );


        $this->add_control(
            'heading_style_description',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Description', 'autozpro'),
                'separator' => 'before',
                'condition' => [
                    'description!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .elementor-cta__description',
                'condition' => [
                    'description!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'description_shadow',
                'selector' => '{{WRAPPER}} .elementor-cta__description',
                'condition' => [
                    'description!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'description_spacing',
            [
                'label' => esc_html__('Spacing', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-content-item.elementor-cta__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'description!' => '',
                ],
            ]
        );


        $this->add_control(
            'heading_content_colors',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Colors', 'autozpro'),
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs('color_tabs');

        $this->start_controls_tab('colors_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__title' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => esc_html__('Sub title Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__subtitle' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementor-cta__subtitle span:before' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Description Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'description!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'colors_hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__('Title Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta:hover .elementor-cta__title' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->add_control(
            'subtitle_color_hover',
            [
                'label' => esc_html__('Sub title Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta:hover .elementor-cta__subtitle' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementor-cta:hover .elementor-cta__subtitle span:before' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->add_control(
            'description_color_hover',
            [
                'label' => esc_html__('Description Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta:hover .elementor-cta__description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'description!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'button_style',
            [
                'label' => esc_html__('Button', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'button!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .elementor-cta__button',
                'condition' => [
                    'button!' => '',
                ],
            ]
        );

        $this->start_controls_tabs('button_tabs');

        $this->start_controls_tab('button_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Text Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__button' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-cta__button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_color',
            [
                'label' => esc_html__('Border Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button-hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => esc_html__('Text Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta .elementor-cta__button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background_color',
            [
                'label' => esc_html__('Background Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-autozpro-banner .elementor-button:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border_width',
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .elementor-cta__button',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__button' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.elementor-widget-autozpro-banner .elementor-button:before' => 'border-radius: {{SIZE}}{{UNIT}};',

                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label' => esc_html__('Margin', 'autozpro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'hover_effects',
            [
                'label' => esc_html__('Hover Effects', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'background_hover_heading',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Background', 'autozpro'),
            ]
        );

        $this->add_control(
            'transformation',
            [
                'label' => esc_html__('Hover Animation', 'autozpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => 'None',
                    'zoom-in' => 'Zoom In',
                    'zoom-out' => 'Zoom Out',
                    'move-up-custom' => 'Move Up',
                    'move-down-custom' => 'Move Down',
                    'move-left-custom' => 'Move Left',
                    'move-right-custom' => 'Move Right',
                ],
                'default' => 'zoom-in',
                'prefix_class' => 'elementor-bg-transform elementor-bg-transform-',
            ]
        );

        $this->start_controls_tabs('bg_effects_tabs');

        $this->start_controls_tab('normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Overlay Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta:not(:hover) .elementor-cta__bg-overlay' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'bg_filters',
                'selector' => '{{WRAPPER}} .elementor-cta__bg',
            ]
        );

        $this->add_control(
            'overlay_blend_mode',
            [
                'label' => esc_html__('Blend Mode', 'autozpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__('Normal', 'autozpro'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'color-burn' => 'Color Burn',
                    'hue' => 'Hue',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'exclusion' => 'Exclusion',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta__bg-overlay' => 'mix-blend-mode: {{VALUE}}',
                ],
                'separator' => 'none',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'overlay_color_hover',
            [
                'label' => esc_html__('Overlay Color', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta:hover .elementor-cta__bg-overlay' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'bg_filters_hover',
                'selector' => '{{WRAPPER}} .elementor-cta:hover .elementor-cta__bg',
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->add_control(
            'effect_duration',
            [
                'label' => esc_html__('Transition Duration', 'autozpro'),
                'type' => Controls_Manager::SLIDER,
                'render_type' => 'template',
                'default' => [
                    'size' => 300,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cta .elementor-cta__bg, {{WRAPPER}} .elementor-cta .elementor-cta__bg-overlay' => 'transition-duration: {{SIZE}}ms',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $title_tag = $settings['title_tag'];
        $wrapper_tag = 'div';
        $button_tag = 'a';
        $bg_image = '';
        $animation_class = '';
        $print_bg = true;


        if (!empty($settings['bg_image']['id'])) {
            $bg_image = Group_Control_Image_Size::get_attachment_image_src($settings['bg_image']['id'], 'bg_image', $settings);
        } elseif (!empty($settings['bg_image']['url'])) {
            $bg_image = $settings['bg_image']['url'];
        }

        if (empty($bg_image)) {
            $print_bg = false;
        }

        $this->add_render_attribute('background_image', 'style', [
            'background-image: url(' . $bg_image . ');',
        ]);

        $this->add_render_attribute('title', 'class', [
            'elementor-cta__title',
            'elementor-cta__content-item',
            'elementor-content-item',
        ]);

        $this->add_render_attribute('subtitle', 'class', [
            'elementor-cta__subtitle',
            'elementor-cta__content-item',
            'elementor-content-item',
        ]);

        $this->add_render_attribute('description', 'class', [
            'elementor-cta__description',
            'elementor-cta__content-item',
            'elementor-content-item',
        ]);

        $this->add_render_attribute('button', 'class', [
            'elementor-cta__button',
            'elementor-button',
        ]);

        if (!empty($settings['link']['url'])) {
            $link_element = 'button';

            if ('box' === $settings['link_click']) {
                $wrapper_tag = 'a';
                $button_tag = 'span';
                $link_element = 'wrapper';
            }

            $this->add_link_attributes($link_element, $settings['link']);
        }

        $this->add_inline_editing_attributes('title');
        $this->add_inline_editing_attributes('description');
        $this->add_inline_editing_attributes('button');

        ?>
        <<?php echo esc_html($wrapper_tag) . ' ' . autozpro_elementor_get_render_attribute_string('wrapper', $this); ?> class="elementor-cta--skin-cover elementor-cta elementor-autozpro-banner">
            <?php if ($print_bg) : ?>
                <div class="elementor-cta__bg-wrapper">
                    <div class="elementor-cta__bg elementor-bg" <?php echo autozpro_elementor_get_render_attribute_string('background_image', $this); ?>></div>
                    <div class="elementor-cta__bg-overlay"></div>
                </div>
            <?php endif; ?>
            <div class="elementor-cta__content">
                <div class="elementor-cta__content_inner">
                    <?php if (!empty($settings['subtitle'])) : ?>
                        <div <?php echo autozpro_elementor_get_render_attribute_string('subtitle', $this); ?>>
                             <span><?php printf('%s', $settings['subtitle']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($settings['title'])) : ?>
                        <<?php echo esc_html($title_tag) . ' ' . autozpro_elementor_get_render_attribute_string('title', $this); ?>>
                            <span><?php printf('%s', $settings['title']); ?></span>
                        </<?php echo esc_html($title_tag); ?>>
                    <?php endif; ?>

                    <?php if (!empty($settings['description'])) : ?>
                        <div <?php echo autozpro_elementor_get_render_attribute_string('description', $this); ?>>
                            <?php printf('%s', $settings['description']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($settings['button'])) : ?>
                        <div class="elementor-cta__button-wrapper elementor-cta__content-item elementor-content-item <?php echo esc_attr($animation_class); ?>">
                            <<?php echo esc_html($button_tag) . ' ' . autozpro_elementor_get_render_attribute_string('button', $this); ?>>
                                <span><?php echo sprintf('%s', $settings['button']); ?><i class="autozpro-icon-angle-right"></i></span>
                            </<?php echo esc_html($button_tag); ?>>
                        </div>
                    <?php endif; ?>
                 </div>
            </div>
        </<?php echo esc_html($wrapper_tag); ?>>
        <?php
    }
}

$widgets_manager->register(new Autozpro_Call_To_Action());
