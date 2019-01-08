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
 * @since             2.0.0
 * @package           Recently
 *
 * @wordpress-plugin
 * Plugin Name:       Recently
 * Plugin URI:        https://wordpress.org/plugins/recently/
 * Description:       A highly customizable & feature-packed recent posts widget.
 * Version:           2.1.0
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

define( 'RECENTLY_VER', '2.1.0' );

/*
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-recently-activator.php';
register_activation_hook( __FILE__, array('Recently_Activator', 'activate') );

/*
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-recently-deactivator.php';
register_deactivation_hook( __FILE__, array('Recently_Deactivator', 'deactivate') );

/*
 * The core plugins class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-recently.php';

/*
 * Begin execution of the plugin.
 */
$recently = new Recently();
$recently->run();
