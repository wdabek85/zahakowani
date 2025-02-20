<?php
/*
Plugin Name: Omnibus — show the lowest price
Text Domain: omnibus
Plugin URI: https://github.com/iworks/omnibus/
Description: The plugin adds two additional fields in the product edit view: the lowest price and the effective date for compatibility with the EU Omnibus Directive.
Version: 3.0.3
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Copyright 2022-2024 Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$includes = dirname( __FILE__ ) . '/includes';

/**
 * require: Iworksomnibus Class
 */
if ( ! class_exists( 'iworks_omnibus' ) ) {
	require_once $includes . '/iworks/class-iworks-omnibus.php';
}

/**
 * i18n
 */
load_plugin_textdomain( 'omnibus', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

new iworks_omnibus();

/**
 * install & uninstall
 */
// register_activation_hook( __FILE__,   'iworks_omnibus_activate' );
// register_deactivation_hook( __FILE__, 'iworks_omnibus_deactivate' );

/**
 * iWorks Rate
 *
 * @since 1.0.2
 */
add_action(
	'init',
	function() {
		include_once dirname( __FILE__ ) . '/includes/iworks/rate/rate.php';
		do_action( 'iworks-register-plugin', plugin_basename( __FILE__ ), __( 'Omnibus', 'omnibus' ), 'omnibus' );
	}
);

