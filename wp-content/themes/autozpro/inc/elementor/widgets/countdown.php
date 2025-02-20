<?php


use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Autozpro_Elementor_Countdown extends Elementor\Widget_Base {

    public function get_name() {
        return 'autozpro-countdown';
    }

    public function get_title() {
        return esc_html__('autozpro Countdown', 'autozpro');
    }

    public function get_categories() {
        return array('autozpro-addons');
    }

    public function get_icon() {
        return 'eicon-countdown';
    }

    public function get_script_depends() {
        return ['autozpro-elementor-countdown'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_countdown',
            [
                'label' => esc_html__('Countdown', 'autozpro'),
            ]
        );

        $this->add_control(
            'countdown_style',
            [
                'label'        => esc_html__('Style', 'autozpro'),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    'style-1' => esc_html__('Style 1', 'autozpro'),
                    'style-2' => esc_html__('Style 2', 'autozpro'),
                    'style-3' => esc_html__('Style 3', 'autozpro'),
                    'style-4' => esc_html__('Style 4', 'autozpro'),
                ],
                'default'      => 'style-1',
                'prefix_class' => 'countdown-'
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => esc_html__('Title', 'autozpro'),
                'type'    => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'due_date',
            [
                'label'       => esc_html__('Due Date', 'autozpro'),
                'type'        => Controls_Manager::DATE_TIME,
                'default'     => date('Y-m-d H:i', strtotime('+1 month') + (get_option('gmt_offset') * HOUR_IN_SECONDS)),
                /* translators: %s: Time zone. */
                'description' => sprintf(esc_html__('Date set according to your timezone: %s.', 'autozpro'), Utils::get_timezone_string()),
            ]
        );

        $this->add_control(
            'show_days',
            [
                'label'     => esc_html__('Days', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__('Show', 'autozpro'),
                'label_off' => esc_html__('Hide', 'autozpro'),
                'default'   => 'yes',
            ]
        );

        $this->add_control(
            'show_hours',
            [
                'label'     => esc_html__('Hours', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__('Show', 'autozpro'),
                'label_off' => esc_html__('Hide', 'autozpro'),
                'default'   => 'yes',
            ]
        );

        $this->add_control(
            'show_minutes',
            [
                'label'     => esc_html__('Minutes', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__('Show', 'autozpro'),
                'label_off' => esc_html__('Hide', 'autozpro'),
                'default'   => 'yes',
            ]
        );

        $this->add_control(
            'show_seconds',
            [
                'label'     => esc_html__('Seconds', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__('Show', 'autozpro'),
                'label_off' => esc_html__('Hide', 'autozpro'),
                'default'   => 'yes',
            ]
        );

        $this->add_control(
            'show_labels',
            [
                'label'     => esc_html__('Show Label', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__('Show', 'autozpro'),
                'label_off' => esc_html__('Hide', 'autozpro'),
                'default'   => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'custom_labels',
            [
                'label'     => esc_html__('Custom Label', 'autozpro'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'show_labels!' => '',
                ],
            ]
        );

        $this->add_control(
            'label_days',
            [
                'label'       => esc_html__('Days', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Days', 'autozpro'),
                'placeholder' => esc_html__('Days', 'autozpro'),
                'condition'   => [
                    'show_labels!'   => '',
                    'custom_labels!' => '',
                    'show_days'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'label_hours',
            [
                'label'       => esc_html__('Hours', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Hours', 'autozpro'),
                'placeholder' => esc_html__('Hours', 'autozpro'),
                'condition'   => [
                    'show_labels!'   => '',
                    'custom_labels!' => '',
                    'show_hours'     => 'yes',
                ],
            ]
        );

        $this->add_control(
            'label_minutes',
            [
                'label'       => esc_html__('Minutes', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Minutes', 'autozpro'),
                'placeholder' => esc_html__('Minutes', 'autozpro'),
                'condition'   => [
                    'show_labels!'   => '',
                    'custom_labels!' => '',
                    'show_minutes'   => 'yes',
                ],
            ]
        );

        $this->add_control(
            'label_seconds',
            [
                'label'       => esc_html__('Seconds', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Seconds', 'autozpro'),
                'placeholder' => esc_html__('Seconds', 'autozpro'),
                'condition'   => [
                    'show_labels!'   => '',
                    'custom_labels!' => '',
                    'show_seconds'   => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_box_style',
            [
                'label' => esc_html__('Boxes', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_width',
            [
                'label'      => esc_html__('Container Width', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-autozpro-countdown' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'items_width',
            [
                'label'      => esc_html__('Items Width', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'width: {{SIZE}}{{UNIT}}; flex-basis: {{SIZE}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );

        $this->add_responsive_control(
            'items_height',
            [
                'label'      => esc_html__('Items Height', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'box_background_color',
            [
                'label'     => esc_html__('Background Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'box_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'box_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

                    '{{WRAPPER}}.elementor-widget-autozpro-countdown .elementor-countdown-item:before' => 'right: -{{RIGHT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'image_border',
                'selector'  => '{{WRAPPER}} .elementor-countdown-item',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label'     => esc_html__('Alignment', 'autozpro'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'autozpro'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'     => [
                        'title' => esc_html__('Center', 'autozpro'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'   => [
                        'title' => esc_html__('Right', 'autozpro'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .elementor-autozpro-countdown' => 'justify-content: {{VALUE}}',
                    '{{WRAPPER}} .countdown-inner' => 'justify-content: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .countdown-title',
            ]
        );

        $this->add_responsive_control(
            'title_alignment',
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
                'label_block' => false,
                'condition' => [
                    'countdown_style' => ['style-1', 'style-4'],
                ],

                'selectors' => [
                    '{{WRAPPER}} .countdown-title' => 'text-align: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .countdown-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .countdown-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_digits_style',
            [
                'label' => esc_html__('Digits', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'digits_typography',
                'selector' => '{{WRAPPER}} .elementor-countdown-digits',
            ]
        );

        $this->add_control(
            'digits_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-digits' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-countdown-item:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'digits_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-digits' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'digits_margin',
            [
                'label'      => esc_html__('Margin', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-digits' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_label_style',
            [
                'label' => esc_html__('Label', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label'     => esc_html__('Color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'label_typography',
                'selector' => '{{WRAPPER}} .elementor-countdown-label',
            ]
        );

        $this->add_responsive_control(
            'label_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_inner_style',
            [
                'label' => esc_html__('Inner', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'countdown_style' => 'style-3',
                ],
            ]
        );
        $this->add_control(
            'inner_background_color',
            [
                'label'     => esc_html__('background color', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .countdown-inner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'inner_alignment',
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
                'label_block' => false,
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'inner_width',
            [
                'label'      => esc_html__('Items Width', 'autozpro'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .countdown-inner' => 'min-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'inner_padding',
            [
                'label'      => esc_html__('Padding', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .countdown-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'inner_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'autozpro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .countdown-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    private function get_strftime($instance) {
        $string = '';
        if ($instance['show_days']) {
            $string .= $this->render_countdown_item($instance, 'label_days', 'days', 'elementor-countdown-days');
        }
        if ($instance['show_hours']) {
            $string .= $this->render_countdown_item($instance, 'label_hours', 'hours', 'elementor-countdown-hours');
        }
        if ($instance['show_minutes']) {
            $string .= $this->render_countdown_item($instance, 'label_minutes', 'minutes', 'elementor-countdown-minutes');
        }
        if ($instance['show_seconds']) {
            $string .= $this->render_countdown_item($instance, 'label_seconds', 'seconds', 'elementor-countdown-seconds');
        }

        return $string;
    }

    private $_default_countdown_labels;

    private function _init_default_countdown_labels() {
        $this->_default_countdown_labels = [
            'label_months'  => esc_html__('Months', 'autozpro'),
            'label_weeks'   => esc_html__('Weeks', 'autozpro'),
            'label_days'    => esc_html__('Days', 'autozpro'),
            'label_hours'   => esc_html__('Hrs', 'autozpro'),
            'label_minutes' => esc_html__('Mins', 'autozpro'),
            'label_seconds' => esc_html__('Secs', 'autozpro'),
        ];
    }

    public function get_default_countdown_labels() {
        if (!$this->_default_countdown_labels) {
            $this->_init_default_countdown_labels();
        }

        return $this->_default_countdown_labels;
    }


    public function render_countdown_item($instance, $label, $name, $part_class) {
        $string = '<div class="elementor-countdown-item ' . esc_attr($name) . '"><span class="elementor-countdown-digits ' . esc_attr($part_class) . '"></span>';

        if ($instance['show_labels']) {
            $default_labels = $this->get_default_countdown_labels();
            $label          = ($instance['custom_labels']) ? $instance[$label] : $default_labels[$label];
            $string         .= ' <span class="elementor-countdown-label">' . esc_html($label) . '</span>';
        }

        $string .= '</div>';

        return $string;
    }

    protected function render() {
        $instance = $this->get_settings();

        $due_date = $instance['due_date'];

        // Handle timezone ( we need to set GMT time )
        $due_date = strtotime($due_date) - (get_option('gmt_offset') * HOUR_IN_SECONDS); ?>
        <div class="countdown-inner">
            <?php if ( !empty($instance['title'])): ?>
                <div class="countdown-title">
                    <?php echo esc_html($instance['title']); ?>
                </div>
            <?php endif; ?>
            <div class="elementor-autozpro-countdown" data-date="<?php echo esc_attr($due_date); ?>">
                <?php echo autozpro_elementor_get_strftime($instance, $this); // WPCS: XSS ok. ?>
            </div>
        </div>
        <?php
    }
}

$widgets_manager->register(new Autozpro_Elementor_Countdown());
