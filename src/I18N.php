<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 *
 * @package    Recently
 */

namespace Recently;

class I18N {
    /**
     * Plugin options.
     *
     * @var     array      $config
     * @access  private
     */
    private $config;

    /**
     * Construct.
     *
     * @since   3.0.0
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        // This is basically a "hack" and should be removed in the future
        // if/when we figure out why Polylang doesn't load Recently's mo files 
        // while WPML does that automatically.
        if ( ! is_admin() && ! $this->config['tools']['data']['ajax'] && function_exists('PLL') ) {
            unload_textdomain('recently');
            load_textdomain('recently', WP_LANG_DIR . '/plugins/recently-' . get_locale() . '.mo');
        }
    }
}
