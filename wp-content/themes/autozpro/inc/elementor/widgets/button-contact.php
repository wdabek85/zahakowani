<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

class Autozpro_Elementor_Button_Contact extends Elementor\Widget_Base {

    public function get_name() {
        return 'button-contact';
    }

    public function get_title() {
        return esc_html__('Autozpro Button Contact', 'autozpro');
    }

    public function get_categories() {
        return ['autozpro-addons'];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'button_contact',
            [
                'label' => esc_html__('Button', 'autozpro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'button_icon',
            [
                'label'       => esc_html__('Icon', 'autozpro'),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'       => esc_html__('Text', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'Help Center',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'content_contact',
            [
                'label' => esc_html__('Content', 'autozpro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        //Header

        $this->add_control(
            'content_header',
            [
                'label'       => esc_html__('Header', 'autozpro'),
                'type'        => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'content_image',
            [
                'label' => esc_html__('Choose Image', 'autozpro'),
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'type' => Controls_Manager::MEDIA,
                'show_label' => false,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'content_header_title',
            [
                'label'       => esc_html__('Header Text', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'Need help?',
            ]
        );

        $this->add_control(
            'content_header_sub_title',
            [
                'label'       => esc_html__('Header Sub', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'Call our product expert',
            ]
        );

        $this->add_control(
            'content_header_description',
            [
                'label'       => esc_html__('Header Description', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => '(406) 555-0120-78',
            ]
        );

        //Footer

        $this->add_control(
            'content_footer',
            [
                'label'       => esc_html__('Footer', 'autozpro'),
                'type'        => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'content_footer_button_text',
            [
                'label'       => esc_html__('Button Text', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'chat with us',
            ]
        );

        $this->add_control(
            'content_footer_button_link',
            [
                'label' => esc_html__('Link to', 'autozpro'),
                'placeholder' => esc_html__('https://your-link.com', 'autozpro'),
                'type' => Controls_Manager::URL,
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'content_footer_title',
            [
                'label'       => esc_html__('Footer Title', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'Mondays - Sundays',
            ]
        );

        $this->add_control(
            'content_footer_description',
            [
                'label'       => esc_html__('Footer Description', 'autozpro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => '7am - 11pm ET | 4am - 8pm PT',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'button_contact_style',
            [
                'label' => esc_html__('Button', 'autozpro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Color Icon', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .button-contact i:nth-child(1)'       => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Color Text', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .button-contact .text'       => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label'     => esc_html__('Background', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-contact-wrapper .button-contact' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color_hover',
            [
                'label'     => esc_html__('Icon Hover', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-contact-wrapper:hover .button-contact i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color_hover',
            [
                'label'     => esc_html__('Background Hover', 'autozpro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-contact-wrapper:hover .button-contact' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'elementor-button-contact-wrapper');
        ?>
            <div <?php echo autozpro_elementor_get_render_attribute_string('wrapper', $this); ?>>

                <div class="button-contact">
                    <?php \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    <?php if (! empty($settings['button_text'])): ?>
                        <span class="text"><?php echo esc_html__($settings['button_text'], 'autozpro'); ?></span>
                    <?php endif; ?>
                    <i class="down autozpro-icon-caret-down"></i>
                </div>

                <div class="content-contact">
                    <div class="content-header">
                        <?php if (!empty($settings['content_image']['url'])) : ?>
                            <div class="image">
                                <?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail', 'content_image'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="right">
                            <?php if (! empty($settings['content_header_title'])): ?>
                                <div class="title"><?php echo esc_html__($settings['content_header_title'], 'autozpro'); ?></div>
                            <?php endif; ?>
                            <?php if (! empty($settings['content_header_sub_title'])): ?>
                                <div class="sub"><?php echo esc_html__($settings['content_header_sub_title'], 'autozpro'); ?></div>
                            <?php endif; ?>
                            <?php if (! empty($settings['content_header_description'])): ?>
                                <div class="description"><?php echo esc_html__($settings['content_header_description'], 'autozpro'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="content-footer">
                        <?php if (! empty($settings['content_footer_button_text'])): ?>
                            <a class="button-footer" href="<?php echo esc_url($settings['content_footer_button_link']['url']); ?>">
                                <i class="autozpro-icon-chat"></i>
                                <span><?php echo esc_html__($settings['content_footer_button_text'], 'autozpro'); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if (! empty($settings['content_footer_title'])): ?>
                            <div class="title"><?php echo esc_html__($settings['content_footer_title'], 'autozpro'); ?></div>
                        <?php endif; ?>
                        <?php if (! empty($settings['content_footer_description'])): ?>
                            <div class="description"><?php echo esc_html__($settings['content_footer_description'], 'autozpro'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php


    }

}

$widgets_manager->register(new Autozpro_Elementor_Button_Contact());
