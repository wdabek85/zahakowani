<?php
declare( strict_types=1 );

/**
 * Plugin Name: Inpost Pay
 * Plugin URI:
 * Description:
 * Version: 1.5.5.4
 * Tested up to: 6.4.3
 * Requires PHP: 7.4
 * Author: iLabs LTD
 * Author URI: iLabs.dev
 * Text Domain: inpost-pay
 * Domain Path: /lang/
 *
 * Copyright 2023 iLabs LTD
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const WOOCOMMERCE_INPOST_PAY_PLUGIN_FILE = __FILE__;

$config = [
	'__FILE__'                => __FILE__,
	'name'                    => 'Inpost Pay',
	'slug'                    => 'inpost_pay',
	'lang_dir'                => 'lang',
	'text_domain'             => 'inpost-pay',
	'min_php_int'             => 70400,
	'min_php'                 => 7.4,
	'required_plugins'        => ['woocommerce'],
	'required_php_extensions' => ['curl'],
];

require_once __DIR__ . '/system.php';

if ( ( new __Inpost_Pay_System( $config ) )->evaluate_system() ) {
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'dependencies.php';

	function inpost_pay(): Ilabs\Inpost_Pay\Plugin {
		return new Ilabs\Inpost_Pay\Plugin();
	}

	inpost_pay()->execute( $config );
}
