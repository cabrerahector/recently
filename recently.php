<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cabrerahector.com/
 * @since             3.0.0
 * @package           Recently
 *
 * @wordpress-plugin
 * Plugin Name:       Recently
 * Plugin URI:        https://wordpress.org/plugins/recently/
 * Description:       A highly customizable & feature-packed recent posts widget.
 * Version:           3.0.5
 * Requires at least: 5.3
 * Requires PHP:      7.2
 * Author:            Hector Cabrera
 * Author URI:        https://cabrerahector.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       recently
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
    die();
}

define('RECENTLY_VERSION', '3.0.5');

$recently_main_plugin_file = __FILE__;
// Load plugin bootstrap
require __DIR__ . '/src/Bootstrap.php';
