<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!autozpro_is_contactform_activated()) {
    return;
}

use Elementor\Controls_Manager;


class Autozpro_Elementor_ContactForm extends Elementor\Widget_Base {

    public function get_name() {
        return 'autozpro-contactform';
    }

    public function get_title() {
        return esc_html__('Autozpro Contact Form', 'autozpro');
    }

    public function get_categories() {
        return array('autozpro-addons');
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'contactform7',
            [
                'label' => esc_html__('General', 'autozpro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $cf7               = get_posts('post_type="wpcf7_contact_form"&numberposts=-1');
        $contact_forms[''] = esc_html__('Please select form', 'autozpro');
        if ($cf7) {
            foreach ($cf7 as $cform) {
                $contact_forms[$cform->ID] = $cform->post_title;
            }
        } else {
            $contact_forms[0] = esc_html__('No contact forms found', 'autozpro');
        }

        $this->add_control(
            'cf_id',
            [
                'label'   => esc_html__('Select contact form', 'autozpro'),
                'type'    => Controls_Manager::SELECT,
                'options' => $contact_forms,
                'default' => ''
            ]
        );

        $this->add_control(
            'form_name',
            [
                'label'   => esc_html__('Form name', 'autozpro'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Contact form', 'autozpro'),
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label'        => esc_html__('Alignment', 'autozpro'),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => [
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
                'prefix_class' => 'contact-form-align-',
                'default'      => '',
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!$settings['cf_id']) {
            return;
        }
        $args['id']    = $settings['cf_id'];
        $args['title'] = $settings['form_name'];

        echo autozpro_do_shortcode('contact-form-7', $args);
    }
}

$widgets_manager->register(new Autozpro_Elementor_ContactForm());
