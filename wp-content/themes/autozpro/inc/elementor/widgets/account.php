<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

class Autozpro_Elementor_Account extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'autozpro-account';
    }

    public function get_title()
    {
        return esc_html__('Autozpro Account', 'autozpro');
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
            'account_title',
            [
                'label' => 'Account title',
                'type' => Controls_Manager::TEXT,
                'default' => 'Hi',
            ]
        );

        $this->add_control(
            'account_text',
            [
                'label' => 'Content Login',
                'type' => Controls_Manager::TEXTAREA,
                'default' => '<span>Sign In</span> or <span>Register</span>',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'account-content',
            [
                'label' => esc_html__('Content', 'autozpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__('Title', 'autozpro'),
                'name' => 'typography_title',
                'selector' => '{{WRAPPER}} .header-group-action .account-title',
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__('Content', 'autozpro'),
                'name' => 'typography_content',
                'selector' => '{{WRAPPER}} .header-group-action .site-header-account',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color Title', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .header-group-action .account-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'name_text_color',
            [
                'label' => esc_html__('Color Content', 'autozpro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .header-group-action .site-header-account a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'elementor-header-account');
        ?>
        <div <?php echo autozpro_elementor_get_render_attribute_string('wrapper', $this); ?>>
            <div class="header-group-action">
                <?php
                if (autozpro_is_woocommerce_activated()) {
                    $account_link = get_permalink(get_option('woocommerce_myaccount_page_id'));
                } else {
                    $account_link = wp_login_url();
                }
                ?>
                <div class="site-header-account">
                    <a href="<?php echo esc_html($account_link); ?>">
                        <div class="account-content">
                            <?php
                            if (!is_user_logged_in()) {
                                ?>
                                <span class="content-content"><?php printf('%s', $settings['account_text']); ?></span>
                                <?php
                            } else {
                                $user = wp_get_current_user(); ?>
                                <div class="account-title"><?php printf('%s', $settings['account_title']); ?></div>
                                <span class="content-admin"><?php echo esc_html($user->display_name); ?></span>
                            <?php } ?>
                        </div>
                    </a>
                    <div class="account-dropdown">

                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

$widgets_manager->register(new Autozpro_Elementor_Account());
