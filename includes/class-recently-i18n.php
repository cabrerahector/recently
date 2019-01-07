<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://cabrerahector.com
 * @since      2.0.0
 *
 * @package    Recently
 * @subpackage Recently/includes
 */
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    Recently
 * @subpackage Recently/includes
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

class Recently_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'recently' );
        load_textdomain( 'recently', WP_LANG_DIR . '/plugins/recently-' . $locale . '.mo' );
    }

} // End Recently_i18n class
