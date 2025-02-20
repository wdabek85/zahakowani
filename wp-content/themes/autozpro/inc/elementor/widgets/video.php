<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;


class Autozpro_Video_Popup extends Elementor\Widget_Base {

    public function get_name() {
        return 'autozpro-video-popup';
    }

    public function get_title() {
        return esc_html__('autozpro Video Popup', 'autozpro');
    }

    public function get_icon() {
        return 'eicon-youtube';
    }

    public function get_script_depends() {
        return ['autozpro-elementor-video', 'magnific-popup'];
    }

    public function get_style_depends() {
        return ['magnific-popup'];
    }


    protected function register_controls() {
        $this->start_controls_section(
            'section_videos',
            [
                'label' => esc_html__('General', 'autozpro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'video_link',
            [
                'label'       => esc_html__('Link to', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__('Support video from Youtube and Vimeo', 'autozpro'),
                'placeholder' => esc_html__('https://your-link.com', 'autozpro'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => esc_html__('Title', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Tile', 'autozpro'),
                'default'     => '',
            ]
        );

        $this->add_responsive_control(
            'video_align',
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .elementor-video-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_font',
            [
                'label'       => esc_html__('Icon Font', 'autozpro'),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'background_video',
            [
                'label'      => esc_html__('Background', 'autozpro'),
                'type'       => Controls_Manager::MEDIA,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default'   => 'full',
                'separator' => 'none',
            ]
        );


        $this->end_controls_section();

        //Wrapper
        $this->start_controls_section(
            'section_video_wrapper',
            [
                'label' => esc_html__('Wrapper', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'autozpro' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [
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
                'selectors' => [
                    '{{WRAPPER}} .elementor-video-popup' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'autozpro' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-video-popup' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wrapper_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-video-popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'wrapper_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-video-popup' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        //Icon
        $this->start_controls_section(
            'section_video_style',
            [
                'label' => esc_html__('Icon', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'video_size',
            [
                'label'     => esc_html__('Font Size', 'autozpro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .autozpro-video-popup .elementor-video-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_video_style');

        $this->start_controls_tab(
            'tab_video_normal',
            [
                'label' => esc_html__('Normal', 'autozpro'),
            ]
        );

        $this->add_control(
            'video_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .autozpro-video-popup .elementor-video-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'video_border_color',
            [
                'label'     => esc_html__('Border Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .autozpro-video-popup .elementor-video-icon' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_video_hover',
            [
                'label' => esc_html__('Hover', 'autozpro'),
            ]
        );

        $this->add_control(
            'video_hover_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .autozpro-video-popup :hover .elementor-video-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'video_border_color_hover',
            [
                'label'     => esc_html__('Border Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .autozpro-video-popup:hover .elementor-video-icon' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'video_hover_box_shadow',
                'selector' => '{{WRAPPER}} .autozpro-video-popup :hover .elementor-video-icon',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();



        $this->add_responsive_control(
            'video_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .autozpro-video-popup .elementor-video-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'video_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .autozpro-video-popup .elementor-video-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        //title
        $this->start_controls_section(
            'section_video_title',
            [
                'label' => esc_html__('Title', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .autozpro-video-popup .elementor-video-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => esc_html__('Color Hover', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-video-popup:hover .elementor-video-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                //'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .autozpro-video-popup .elementor-video-title',
            ]
        );

        $this->add_control(
            'show_title_block',
            [
                'label'     => esc_html__('Style Block', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'autozpro'),
                'label_on'  => esc_html__('On', 'autozpro'),
                'selectors' => [
                    '{{WRAPPER}} .autozpro-video-popup .elementor-video-popup' => 'flex-direction: column;',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (empty($settings['video_link'])) {
            return;
        }

        $this->add_render_attribute('wrapper', 'class', 'elementor-video-wrapper');
        $this->add_render_attribute('wrapper', 'class', 'autozpro-video-popup');

        $this->add_render_attribute('button', 'class', 'elementor-video-popup');
        $this->add_render_attribute('button', 'role', 'button');
        $this->add_render_attribute('button', 'href', esc_url($settings['video_link']));
        $this->add_render_attribute('button', 'data-effect', 'mfp-zoom-in');

        $titleHtml = !empty($settings['title']) ? '<span class="elementor-video-title">' . $settings['title'] . '</span>' : '';

        ?>
        <div <?php echo autozpro_elementor_get_render_attribute_string('wrapper', $this); ?>>
            <a <?php echo autozpro_elementor_get_render_attribute_string('button', $this); ?>>
                <?php printf('%s', $titleHtml); ?>
                <span class="elementor-video-icon">
                    <?php \Elementor\Icons_Manager::render_icon( $settings['icon_font'], [ 'aria-hidden' => 'true' ] ); ?>
                </span>

                <?php if (!empty($settings['background_video']['url'])) :?>
                    <span class="image-hover rotate">
                        <?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail', 'background_video'); ?>
                    </span>
                <?php endif; ?>

            </a>
        </div>
        <?php
    }

}

$widgets_manager->register(new Autozpro_Video_Popup());
