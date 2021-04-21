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
 * Version:           3.0.3
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

define('RECENTLY_VERSION', '3.0.3');
define('RECENTLY_MIN_PHP_VERSION', '5.4');
define('RECENTLY_MIN_WP_VERSION', '4.9');

/** Requirements check */
global $wp_version;

// We're good, continue!
if ( version_compare(PHP_VERSION, RECENTLY_MIN_PHP_VERSION, '>=') && version_compare($wp_version, RECENTLY_MIN_WP_VERSION, '>=') ) {
    $recently_main_plugin_file = __FILE__;
    // Load plugin bootstrap
    require __DIR__ . '/src/Bootstrap.php';
} // Nope.
else {
    if ( isset($_GET['activate']) )
        unset($_GET['activate']);

    function recently_render_min_requirements_notice() {
        global $wp_version;
        echo '<div class="notice notice-error"><p>' . sprintf(
            __('Recently requires at least PHP %1$s and WordPress %2$s to function correctly. Your site uses PHP %3$s and WordPress %4$s.', 'recently'),
            RECENTLY_MIN_PHP_VERSION,
            RECENTLY_MIN_WP_VERSION,
            PHP_VERSION,
            $wp_version
        ) . '</p></div>';
    }
    add_action('admin_notices', 'recently_render_min_requirements_notice');
}
