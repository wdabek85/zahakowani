<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Autozpro_Customize')) {

    class Autozpro_Customize {


        public function __construct() {
            add_action('customize_register', array($this, 'customize_register'));
        }

        public function get_banner() {
            global $post;

            $options[''] = esc_html__('Select Banner', 'autozpro');
            if (!autozpro_is_elementor_activated()) {
                return;
            }
            $args = array(
                'post_type'      => 'elementor_library',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                's'              => 'Banner ',
                'order'          => 'ASC',
            );

            $query1 = new WP_Query($args);
            while ($query1->have_posts()) {
                $query1->the_post();
                $options[$post->post_name] = $post->post_title;
            }

            wp_reset_postdata();
            return $options;
        }

        public function get_search_compatibility() {
            global $post;
            $options[''] = esc_html__('Select search Compatibility', 'autozpro');
            if (!autozpro_is_elementor_activated()) {
                return;
            }
            $args = array(
                'post_type'      => 'elementor_library',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                's'              => 'Search ',
                'order'          => 'ASC',
            );

            $query1 = new WP_Query($args);
            while ($query1->have_posts()) {
                $query1->the_post();
                $options[$post->post_name] = $post->post_title;
            }

            wp_reset_postdata();
            return $options;
        }

        /**
         * @param $wp_customize WP_Customize_Manager
         */
        public function customize_register($wp_customize) {

            /**
             * Theme options.
             */
            require_once get_theme_file_path('inc/customize-control/editor.php');
            $this->init_autozpro_blog($wp_customize);

            if (autozpro_is_woocommerce_activated()) {
                $this->init_woocommerce($wp_customize);
            }

            do_action('autozpro_customize_register', $wp_customize);
        }


        /**
         * @param $wp_customize WP_Customize_Manager
         *
         * @return void
         */
        public function init_autozpro_blog($wp_customize) {

            $wp_customize->add_section('autozpro_blog_archive', array(
                'title' => esc_html__('Blog', 'autozpro'),
            ));

            // =========================================
            // Select Style
            // =========================================

            $wp_customize->add_setting('autozpro_options_blog_style', array(
                'type'              => 'option',
                'default'           => 'standard',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_blog_style', array(
                'section' => 'autozpro_blog_archive',
                'label'   => esc_html__('Blog style', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    'standard' => esc_html__('Blog Standard', 'autozpro'),
                    'grid'     => esc_html__('Blog Grid', 'autozpro'),
                    'list'     => esc_html__('Blog List', 'autozpro'),
                ),
            ));

            $wp_customize->add_setting('autozpro_options_blog_columns', array(
                'type'              => 'option',
                'default'           => 1,
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_blog_columns', array(
                'section' => 'autozpro_blog_archive',
                'label'   => esc_html__('Colunms (for Blog Grid)', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    1 => esc_html__('1', 'autozpro'),
                    2 => esc_html__('2', 'autozpro'),
                    3 => esc_html__('3', 'autozpro'),
                    4 => esc_html__('4', 'autozpro'),
                ),
            ));
        }

        /**
         * @param $wp_customize WP_Customize_Manager
         *
         * @return void
         */
        public function init_woocommerce($wp_customize) {

            $wp_customize->add_panel('woocommerce', array(
                'title' => esc_html__('Woocommerce', 'autozpro'),
            ));


            $wp_customize->add_section('autozpro_woocommerce_archive', array(
                'title'      => esc_html__('Archive', 'autozpro'),
                'capability' => 'edit_theme_options',
                'panel'      => 'woocommerce',
                'priority'   => 1,
            ));
            if (autozpro_is_elementor_activated()) {
                $wp_customize->add_setting('autozpro_options_shop_banner', array(
                    'type'              => 'option',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ));

                $wp_customize->add_control('autozpro_options_shop_banner', array(
                    'section' => 'autozpro_woocommerce_archive',
                    'label'   => esc_html__('Banner', 'autozpro'),
                    'type'    => 'select',
                    'description' => __( 'Banner will take templates name prefix is "Banner"', 'autozpro' ),
                    'choices' => $this->get_banner()
                ));

                $wp_customize->add_setting('autozpro_options_shop_search_compatibility', array(
                    'type'              => 'option',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ));

                $wp_customize->add_control('autozpro_options_shop_search_compatibility', array(
                    'section' => 'autozpro_woocommerce_archive',
                    'label'   => esc_html__('Search Compatibility', 'autozpro'),
                    'type'    => 'select',
                    'description' => __( 'Search compatibility will take templates name prefix is "Search"', 'autozpro' ),
                    'choices' => $this->get_search_compatibility()
                ));

                $wp_customize->add_setting('autozpro_options_shop_search_position', array(
                    'type'              => 'option',
                    'default'           => 'sidebar',
                    'sanitize_callback' => 'sanitize_text_field',
                ));

                $wp_customize->add_control('autozpro_options_shop_search_position', array(
                    'section' => 'autozpro_woocommerce_archive',
                    'label'   => esc_html__('Search Compatibility Position', 'autozpro'),
                    'type'    => 'select',
                    'default' => 'sidebar',
                    'choices' => array(
                        'top'    => esc_html__('Top Shop', 'autozpro'),
                        'sidebar' => esc_html__('Sidebar', 'autozpro'),
                    ),
                ));

            }

            $wp_customize->add_setting('autozpro_options_woocommerce_archive_layout', array(
                'type'              => 'option',
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_woocommerce_archive_layout', array(
                'section' => 'autozpro_woocommerce_archive',
                'label'   => esc_html__('Layout Style', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    'default'  => esc_html__('Sidebar', 'autozpro'),
                    //====start_premium
                    'canvas'   => esc_html__('Canvas Filter', 'autozpro'),
                    'dropdown' => esc_html__('Dropdown Filter', 'autozpro'),
                    //====end_premium
                ),
            ));

            $wp_customize->add_setting('autozpro_options_woocommerce_archive_width', array(
                'type'              => 'option',
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_woocommerce_archive_width', array(
                'section' => 'autozpro_woocommerce_archive',
                'label'   => esc_html__('Layout Width', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    'default' => esc_html__('Default', 'autozpro'),
                    'wide'    => esc_html__('Wide', 'autozpro'),
                ),
            ));

            $wp_customize->add_setting('autozpro_options_woocommerce_archive_sidebar', array(
                'type'              => 'option',
                'default'           => 'left',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_woocommerce_archive_sidebar', array(
                'section' => 'autozpro_woocommerce_archive',
                'label'   => esc_html__('Sidebar Position', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    'left'  => esc_html__('Left', 'autozpro'),
                    'right' => esc_html__('Right', 'autozpro'),

                ),
            ));

            // =========================================
            // Single Product
            // =========================================

            $wp_customize->add_section('autozpro_woocommerce_single', array(
                'title'      => esc_html__('Single Product', 'autozpro'),
                'capability' => 'edit_theme_options',
                'panel'      => 'woocommerce',
            ));

            $wp_customize->add_setting('autozpro_options_single_product_gallery_layout', array(
                'type'              => 'option',
                'default'           => 'horizontal',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('autozpro_options_single_product_gallery_layout', array(
                'section' => 'autozpro_woocommerce_single',
                'label'   => esc_html__('Style', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    'horizontal' => esc_html__('Horizontal', 'autozpro'),
                    //====start_premium
                    'vertical'   => esc_html__('Vertical', 'autozpro'),
                    'gallery'    => esc_html__('Gallery', 'autozpro'),
                    'sticky'     => esc_html__('Sticky', 'autozpro'),
                    //====end_premium
                ),
            ));

            $wp_customize->add_setting('autozpro_options_single_product_content_meta', array(
                'type'              => 'option',
                'sanitize_callback' => 'wp_kses_post',
                'transport'         => 'postMessage',
            ));

            $wp_customize->add_control('autozpro_options_single_product_content_meta', array(
                'section' => 'autozpro_woocommerce_single',
                'type'    => 'textarea',
                'label'   => esc_html__('Single extra description', 'autozpro'),
            ));

            $wp_customize->add_setting('autozpro_options_single_product_archive_sidebar', array(
                'type'              => 'option',
                'default'           => 'left',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('autozpro_options_single_product_archive_sidebar', array(
                'section' => 'autozpro_woocommerce_single',
                'label'   => esc_html__('Sidebar Position', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    ''      => esc_html__('None', 'autozpro'),
                    'left'  => esc_html__('Left', 'autozpro'),
                    'right' => esc_html__('Right', 'autozpro'),

                ),
            ));


            // =========================================
            // Product
            // =========================================

            $wp_customize->add_section('autozpro_woocommerce_product', array(
                'title'      => esc_html__('Product Block', 'autozpro'),
                'capability' => 'edit_theme_options',
                'panel'      => 'woocommerce',
            ));

            $wp_customize->add_setting('autozpro_options_wocommerce_block_style', array(
                'type'              => 'option',
                'default'           => '',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('autozpro_options_wocommerce_block_style', array(
                'section' => 'autozpro_woocommerce_product',
                'label'   => esc_html__('Style', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    '' => esc_html__('Style 1', 'autozpro'),
                ),
            ));

            $wp_customize->add_setting('autozpro_options_woocommerce_product_hover', array(
                'type'              => 'option',
                'default'           => 'none',
                'transport'         => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('autozpro_options_woocommerce_product_hover', array(
                'section' => 'autozpro_woocommerce_product',
                'label'   => esc_html__('Animation Image Hover', 'autozpro'),
                'type'    => 'select',
                'choices' => array(
                    'none'          => esc_html__('None', 'autozpro'),
                    'bottom-to-top' => esc_html__('Bottom to Top', 'autozpro'),
                    'top-to-bottom' => esc_html__('Top to Bottom', 'autozpro'),
                    'right-to-left' => esc_html__('Right to Left', 'autozpro'),
                    'left-to-right' => esc_html__('Left to Right', 'autozpro'),
                    'swap'          => esc_html__('Swap', 'autozpro'),
                    'fade'          => esc_html__('Fade', 'autozpro'),
                    'zoom-in'       => esc_html__('Zoom In', 'autozpro'),
                    'zoom-out'      => esc_html__('Zoom Out', 'autozpro'),
                ),
            ));

            $wp_customize->add_setting('autozpro_options_wocommerce_row_laptop', array(
                'type'              => 'option',
                'default'           => 3,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_wocommerce_row_laptop', array(
                'section' => 'woocommerce_product_catalog',
                'label'   => esc_html__('Products per row Laptop', 'autozpro'),
                'type'    => 'number',
            ));

            $wp_customize->add_setting('autozpro_options_wocommerce_row_tablet', array(
                'type'              => 'option',
                'default'           => 2,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_wocommerce_row_tablet', array(
                'section' => 'woocommerce_product_catalog',
                'label'   => esc_html__('Products per row tablet', 'autozpro'),
                'type'    => 'number',
            ));

            $wp_customize->add_setting('autozpro_options_wocommerce_row_mobile', array(
                'type'              => 'option',
                'default'           => 1,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('autozpro_options_wocommerce_row_mobile', array(
                'section' => 'woocommerce_product_catalog',
                'label'   => esc_html__('Products per row mobile', 'autozpro'),
                'type'    => 'number',
            ));
        }
    }
}
return new Autozpro_Customize();
