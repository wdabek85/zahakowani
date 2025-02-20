<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Autozpro_Elementor')) :

    /**
     * The Autozpro Elementor Integration class
     */
    class Autozpro_Elementor {
        private $suffix = '';

        public function __construct() {
            $this->suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

            add_action('elementor/frontend/after_enqueue_scripts', [$this, 'register_auto_scripts_frontend']);
            add_action('elementor/init', array($this, 'add_category'));
            add_action('wp_enqueue_scripts', [$this, 'add_scripts'], 15);
            add_action('elementor/widgets/register', array($this, 'customs_widgets'));
            add_action('elementor/widgets/register', array($this, 'include_widgets'));
            add_action('elementor/frontend/after_enqueue_scripts', [$this, 'add_js']);

            // Custom Animation Scroll
            add_filter('elementor/controls/animations/additional_animations', [$this, 'add_animations_scroll']);

            // Elementor Fix Noitice WooCommerce
            add_action('elementor/editor/before_enqueue_scripts', array($this, 'woocommerce_fix_notice'));

            // Backend
            add_action('elementor/editor/after_enqueue_styles', [$this, 'add_style_editor'], 99);

            // Add Icon Custom
            add_action('elementor/icons_manager/native', [$this, 'add_icons_native']);
            add_action('elementor/controls/register', [$this, 'add_icons']);

            // Add Breakpoints
            add_action('wp_enqueue_scripts', 'autozpro_elementor_breakpoints', 9999);

            if (!autozpro_is_elementor_pro_activated()) {
                require trailingslashit(get_template_directory()) . 'inc/elementor/custom-css.php';
                require trailingslashit(get_template_directory()) . 'inc/elementor/sticky-section.php';
                if (is_admin()) {
                    add_action('manage_elementor_library_posts_columns', [$this, 'admin_columns_headers']);
                    add_action('manage_elementor_library_posts_custom_column', [$this, 'admin_columns_content'], 10, 2);
                }
            }

            add_filter('elementor/fonts/additional_fonts', [$this, 'additional_fonts']);
            add_action('wp_enqueue_scripts', [$this, 'elementor_kit']);
        }

        public function elementor_kit() {
            $active_kit_id = Elementor\Plugin::$instance->kits_manager->get_active_id();
            Elementor\Plugin::$instance->kits_manager->frontend_before_enqueue_styles();
            $myvals = get_post_meta($active_kit_id, '_elementor_page_settings', true);
            if (!empty($myvals)) {
                $css = '';
                foreach ($myvals['system_colors'] as $key => $value) {
                    $css .= $value['color'] !== '' ? '--' . $value['_id'] . ':' . $value['color'] . ';' : '';
                }

                $var = "body{{$css}}";
                wp_add_inline_style('autozpro-style', $var);
            }
        }

        public function additional_fonts($fonts) {
            $fonts["Manrope"] = 'googlefonts';
            return $fonts;
        }

        public function admin_columns_headers($defaults) {
            $defaults['shortcode'] = esc_html__('Shortcode', 'autozpro');

            return $defaults;
        }

        public function admin_columns_content($column_name, $post_id) {
            if ('shortcode' === $column_name) {
                ob_start();
                ?>
                <input class="elementor-shortcode-input" type="text" readonly onfocus="this.select()" value="[hfe_template id='<?php echo esc_attr($post_id); ?>']"/>
                <?php
                ob_get_contents();
            }
        }

        public function add_js() {
            global $autozpro_version;
            wp_enqueue_script('autozpro-elementor-frontend', get_theme_file_uri('/assets/js/elementor-frontend.js'), [], $autozpro_version);
        }

        public function add_style_editor() {
            global $autozpro_version;
            wp_enqueue_style('autozpro-elementor-editor-icon', get_theme_file_uri('/assets/css/admin/elementor/icons.css'), [], $autozpro_version);
        }

        public function add_scripts() {
            global $autozpro_version;
            $suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
            wp_enqueue_style('autozpro-elementor', get_template_directory_uri() . '/assets/css/base/elementor.css', '', $autozpro_version);
            wp_style_add_data('autozpro-elementor', 'rtl', 'replace');

            wp_register_script('jquery-elementor-select2', ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full' . $suffix . '.js', ['jquery',], '4.0.6-rc.1', true);
            wp_register_style('elementor-select2', ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2' . $suffix . '.css', [], '4.0.6-rc.1'
            );
            // Add Scripts
            wp_register_script('tweenmax', get_theme_file_uri('/assets/js/vendor/TweenMax.min.js'), array('jquery'), '1.11.1');
            wp_register_script('parallaxmouse', get_theme_file_uri('/assets/js/vendor/jquery-parallax.js'), array('jquery'), $autozpro_version);

            if (autozpro_elementor_check_type('animated-bg-parallax')) {
                wp_enqueue_script('tweenmax');
                wp_enqueue_script('jquery-panr', get_theme_file_uri('/assets/js/vendor/jquery-panr' . $suffix . '.js'), array('jquery'), '0.0.1');
            }
        }


        public function register_auto_scripts_frontend() {
            global $autozpro_version;
            wp_register_script('autozpro-elementor-accordion', get_theme_file_uri('/assets/js/elementor/accordion.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-brand', get_theme_file_uri('/assets/js/elementor/brand.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-countdown', get_theme_file_uri('/assets/js/elementor/countdown.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-image-gallery', get_theme_file_uri('/assets/js/elementor/image-gallery.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-posts-grid', get_theme_file_uri('/assets/js/elementor/posts-grid.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-product-categories', get_theme_file_uri('/assets/js/elementor/product-categories.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-product-related', get_theme_file_uri('/assets/js/elementor/product-related.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-product-tab', get_theme_file_uri('/assets/js/elementor/product-tab.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-products', get_theme_file_uri('/assets/js/elementor/products.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-search-compatibility', get_theme_file_uri('/assets/js/elementor/search-compatibility.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-tabs', get_theme_file_uri('/assets/js/elementor/tabs.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-testimonial', get_theme_file_uri('/assets/js/elementor/testimonial.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
            wp_register_script('autozpro-elementor-video', get_theme_file_uri('/assets/js/elementor/video.js'), array('jquery','elementor-frontend'), $autozpro_version, true);
           
        }

        public function add_category() {
            Elementor\Plugin::instance()->elements_manager->add_category(
                'autozpro-addons',
                array(
                    'title' => esc_html__('Autozpro Addons', 'autozpro'),
                    'icon'  => 'fa fa-plug',
                ),
                1);
        }

        public function add_animations_scroll($animations) {
            $animations['Autozpro Animation'] = [
                'opal-move-up'    => 'Move Up',
                'opal-move-down'  => 'Move Down',
                'opal-move-left'  => 'Move Left',
                'opal-move-right' => 'Move Right',
                'opal-flip'       => 'Flip',
                'opal-helix'      => 'Helix',
                'opal-scale-up'   => 'Scale',
                'opal-am-popup'   => 'Popup',
            ];

            return $animations;
        }

        public function customs_widgets() {
            $files = glob(get_theme_file_path('/inc/elementor/custom-widgets/*.php'));
            foreach ($files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }

        /**
         * @param $widgets_manager Elementor\Widgets_Manager
         */
        public function include_widgets($widgets_manager) {
            $files = glob(get_theme_file_path('/inc/elementor/widgets/*.php'));
            foreach ($files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }

        public function woocommerce_fix_notice() {
            if (autozpro_is_woocommerce_activated()) {
                remove_action('woocommerce_cart_is_empty', 'woocommerce_output_all_notices', 5);
                remove_action('woocommerce_shortcode_before_product_cat_loop', 'woocommerce_output_all_notices', 10);
                remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
                remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10);
                remove_action('woocommerce_before_cart', 'woocommerce_output_all_notices', 10);
                remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);
                remove_action('woocommerce_account_content', 'woocommerce_output_all_notices', 10);
                remove_action('woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10);
            }
        }

        public function add_icons( $manager ) {
            $new_icons = json_decode( '{"autozpro-icon-account":"account","autozpro-icon-address":"address","autozpro-icon-angle-down":"angle-down","autozpro-icon-angle-left":"angle-left","autozpro-icon-angle-right":"angle-right","autozpro-icon-angle-up":"angle-up","autozpro-icon-arrow-left":"arrow-left","autozpro-icon-arrow-right":"arrow-right","autozpro-icon-bag-alt":"bag-alt","autozpro-icon-calendar":"calendar","autozpro-icon-cart":"cart","autozpro-icon-chat":"chat","autozpro-icon-check-a":"check-a","autozpro-icon-check-dot":"check-dot","autozpro-icon-check-square-solid":"check-square-solid","autozpro-icon-clock":"clock","autozpro-icon-compare":"compare","autozpro-icon-dot-1":"dot-1","autozpro-icon-eye":"eye","autozpro-icon-facebook-f":"facebook-f","autozpro-icon-featured":"featured","autozpro-icon-filter-ul":"filter-ul","autozpro-icon-google-plus-g":"google-plus-g","autozpro-icon-help-center":"help-center","autozpro-icon-laurel-star":"laurel-star","autozpro-icon-linkedin-in":"linkedin-in","autozpro-icon-list-ul":"list-ul","autozpro-icon-long-arrow-left":"long-arrow-left","autozpro-icon-long-arrow-right":"long-arrow-right","autozpro-icon-pen":"pen","autozpro-icon-phone":"phone","autozpro-icon-play":"play","autozpro-icon-popular":"popular","autozpro-icon-prize":"prize","autozpro-icon-quote":"quote","autozpro-icon-shopping-bag":"shopping-bag","autozpro-icon-sliders-v":"sliders-v","autozpro-icon-steering":"steering","autozpro-icon-tools":"tools","autozpro-icon-truck-check":"truck-check","autozpro-icon-360":"360","autozpro-icon-bars":"bars","autozpro-icon-caret-down":"caret-down","autozpro-icon-caret-left":"caret-left","autozpro-icon-caret-right":"caret-right","autozpro-icon-caret-up":"caret-up","autozpro-icon-cart-empty":"cart-empty","autozpro-icon-check-square":"check-square","autozpro-icon-circle":"circle","autozpro-icon-cloud-download-alt":"cloud-download-alt","autozpro-icon-comment":"comment","autozpro-icon-comments":"comments","autozpro-icon-contact":"contact","autozpro-icon-credit-card":"credit-card","autozpro-icon-dot-circle":"dot-circle","autozpro-icon-edit":"edit","autozpro-icon-envelope":"envelope","autozpro-icon-expand-alt":"expand-alt","autozpro-icon-external-link-alt":"external-link-alt","autozpro-icon-file-alt":"file-alt","autozpro-icon-file-archive":"file-archive","autozpro-icon-filter":"filter","autozpro-icon-folder-open":"folder-open","autozpro-icon-folder":"folder","autozpro-icon-frown":"frown","autozpro-icon-gift":"gift","autozpro-icon-grid":"grid","autozpro-icon-grip-horizontal":"grip-horizontal","autozpro-icon-heart-fill":"heart-fill","autozpro-icon-heart":"heart","autozpro-icon-history":"history","autozpro-icon-home":"home","autozpro-icon-info-circle":"info-circle","autozpro-icon-instagram":"instagram","autozpro-icon-level-up-alt":"level-up-alt","autozpro-icon-list":"list","autozpro-icon-map-marker-check":"map-marker-check","autozpro-icon-meh":"meh","autozpro-icon-minus-circle":"minus-circle","autozpro-icon-minus":"minus","autozpro-icon-mobile-android-alt":"mobile-android-alt","autozpro-icon-money-bill":"money-bill","autozpro-icon-pencil-alt":"pencil-alt","autozpro-icon-plus-circle":"plus-circle","autozpro-icon-plus":"plus","autozpro-icon-random":"random","autozpro-icon-reply-all":"reply-all","autozpro-icon-reply":"reply","autozpro-icon-search-plus":"search-plus","autozpro-icon-search":"search","autozpro-icon-shield-check":"shield-check","autozpro-icon-shopping-basket":"shopping-basket","autozpro-icon-shopping-cart":"shopping-cart","autozpro-icon-sign-out-alt":"sign-out-alt","autozpro-icon-smile":"smile","autozpro-icon-spinner":"spinner","autozpro-icon-square":"square","autozpro-icon-star":"star","autozpro-icon-store":"store","autozpro-icon-sync":"sync","autozpro-icon-tachometer-alt":"tachometer-alt","autozpro-icon-th-large":"th-large","autozpro-icon-th-list":"th-list","autozpro-icon-thumbtack":"thumbtack","autozpro-icon-ticket":"ticket","autozpro-icon-times-circle":"times-circle","autozpro-icon-times":"times","autozpro-icon-trophy-alt":"trophy-alt","autozpro-icon-truck":"truck","autozpro-icon-user-headset":"user-headset","autozpro-icon-user-shield":"user-shield","autozpro-icon-user":"user","autozpro-icon-video":"video","autozpro-icon-wishlist-empty":"wishlist-empty","autozpro-icon-adobe":"adobe","autozpro-icon-amazon":"amazon","autozpro-icon-android":"android","autozpro-icon-angular":"angular","autozpro-icon-apper":"apper","autozpro-icon-apple":"apple","autozpro-icon-atlassian":"atlassian","autozpro-icon-behance":"behance","autozpro-icon-bitbucket":"bitbucket","autozpro-icon-bitcoin":"bitcoin","autozpro-icon-bity":"bity","autozpro-icon-bluetooth":"bluetooth","autozpro-icon-btc":"btc","autozpro-icon-centos":"centos","autozpro-icon-chrome":"chrome","autozpro-icon-codepen":"codepen","autozpro-icon-cpanel":"cpanel","autozpro-icon-discord":"discord","autozpro-icon-dochub":"dochub","autozpro-icon-docker":"docker","autozpro-icon-dribbble":"dribbble","autozpro-icon-dropbox":"dropbox","autozpro-icon-drupal":"drupal","autozpro-icon-ebay":"ebay","autozpro-icon-facebook":"facebook","autozpro-icon-figma":"figma","autozpro-icon-firefox":"firefox","autozpro-icon-google-plus":"google-plus","autozpro-icon-google":"google","autozpro-icon-grunt":"grunt","autozpro-icon-gulp":"gulp","autozpro-icon-html5":"html5","autozpro-icon-joomla":"joomla","autozpro-icon-link-brand":"link-brand","autozpro-icon-linkedin":"linkedin","autozpro-icon-mailchimp":"mailchimp","autozpro-icon-opencart":"opencart","autozpro-icon-paypal":"paypal","autozpro-icon-pinterest-p":"pinterest-p","autozpro-icon-reddit":"reddit","autozpro-icon-skype":"skype","autozpro-icon-slack":"slack","autozpro-icon-snapchat":"snapchat","autozpro-icon-spotify":"spotify","autozpro-icon-trello":"trello","autozpro-icon-twitter-t":"twitter-t","autozpro-icon-twitter":"twitter","autozpro-icon-vimeo":"vimeo","autozpro-icon-whatsapp":"whatsapp","autozpro-icon-wordpress":"wordpress","autozpro-icon-yoast":"yoast","autozpro-icon-youtube":"youtube"}', true );
			$icons     = $manager->get_control( 'icon' )->get_settings( 'options' );
			$new_icons = array_merge(
				$new_icons,
				$icons
			);
			// Then we set a new list of icons as the options of the icon control
			$manager->get_control( 'icon' )->set_settings( 'options', $new_icons ); 
        }

        public function add_icons_native($tabs) {
            global $autozpro_version;
            $tabs['opal-custom'] = [
                'name'          => 'autozpro-icon',
                'label'         => esc_html__('Autozpro Icon', 'autozpro'),
                'prefix'        => 'autozpro-icon-',
                'displayPrefix' => 'autozpro-icon-',
                'labelIcon'     => 'fab fa-font-awesome-alt',
                'ver'           => $autozpro_version,
                'fetchJson'     => get_theme_file_uri('/inc/elementor/icons.json'),
                'native'        => true,
            ];

            return $tabs;
        }
    }

endif;

return new Autozpro_Elementor();
