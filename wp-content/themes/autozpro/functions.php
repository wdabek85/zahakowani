<?php
$theme            = wp_get_theme('autozpro');
$autozpro_version = $theme['Version'];
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
    $content_width = 980; /* pixels */
}
require get_theme_file_path('inc/class-tgm-plugin-activation.php');
$autozpro = (object)array(
    'version' => $autozpro_version,
    /**
     * Initialize all the things.
     */
    'main'    => require 'inc/class-main.php',
);

require get_theme_file_path('inc/functions.php');
require get_theme_file_path('inc/template-hooks.php');
require get_theme_file_path('inc/template-functions.php');

require_once get_theme_file_path('inc/merlin/vendor/autoload.php');
require_once get_theme_file_path('inc/merlin/class-merlin.php');
require_once get_theme_file_path('inc/merlin-config.php');

require_once get_theme_file_path('inc/class-customize.php');

if (autozpro_is_woocommerce_activated()) {
    $autozpro->woocommerce = require get_theme_file_path('inc/woocommerce/class-woocommerce.php');

    require get_theme_file_path('inc/woocommerce/class-woocommerce-adjacent-products.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-walker-category.php');

    require get_theme_file_path('inc/woocommerce/woocommerce-functions.php');
    require get_theme_file_path('inc/woocommerce/woocommerce-template-functions.php');
    require get_theme_file_path('inc/woocommerce/woocommerce-template-hooks.php');
    require get_theme_file_path('inc/woocommerce/template-hooks.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-settings.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-brand.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-extra.php');
    require get_theme_file_path('inc/woocommerce/class-woocommerce-autoparts.php');
    require get_theme_file_path('inc/merlin/includes/class-wc-widget-product-brands.php');
    require get_theme_file_path('inc/merlin/includes/class-wc-widget-autoparts_search.php');
    require get_theme_file_path('inc/merlin/includes/product-360-view.php');
}

if (autozpro_is_elementor_activated()) {
    require get_theme_file_path('inc/elementor/functions-elementor.php');

    if (!defined('ELEMENTOR_PRO_VERSION') && version_compare( ELEMENTOR_VERSION, '3.18.0', '>=' )) {
        require get_theme_file_path('inc/elementor/class-fix-elementor.php');
    }

    $autozpro->elementor = require get_theme_file_path('inc/elementor/class-elementor.php');
    //====start_premium
    $autozpro->megamenu = require get_theme_file_path('inc/megamenu/megamenu.php');
    //====end_premium
    $autozpro->parallax = require get_theme_file_path('inc/elementor/section-parallax.php');
    if (defined('ELEMENTOR_PRO_VERSION')) {
        require get_theme_file_path('inc/elementor/class-elementor-pro.php');
    }

    if (function_exists('hfe_init')) {
        require get_theme_file_path('inc/header-footer-elementor/class-hfe.php');
        require get_theme_file_path('inc/merlin/includes/breadcrumb.php');
    }

    if (autozpro_is_woocommerce_activated()) {
        require_once get_theme_file_path('inc/elementor/elementor-control/class-elementor-control.php');
    }

}

if (!is_user_logged_in()) {
    require get_theme_file_path('inc/modules/class-login.php');
}
