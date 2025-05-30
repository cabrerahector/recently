<?php
/**
 * Reloads plugin's .mo file when Polylang is present.
 *
 * @package    Recently
 * @subpackage Recently/Compatibility
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Compatibility\Polylang;

use Recently\Compatibility\Compat;

class Polylang extends Compat
{
    /**
     * Admin settings.
     *
     * @since  4.2.0
     * @var array
     * @access protected
     */
    private $config;

    /**
     * Construct.
     *
     * @param array $settings
     */
    public function __construct($settings)
    {
        $this->config = $settings;
    }

    /**
     * Hooks into init to load current textdomain.
     */
    public function init()
    {
        if ( function_exists('PLL') ) {
            add_action('init', [$this, 'load_textdomain']);
        }
    }

    /**
     * Loads current textdomain.
     */
    public function load_textdomain() {
        // This is basically a "hack" and should be removed in the future
        // if/when we figure out why Polylang doesn't load Recently's mo files 
        // while WPML does automatically.

        $locale_file = WP_LANG_DIR . '/plugins/recently-' . get_locale() . '.mo';

        if (
            ! is_admin()
            && ! $this->config['tools']['data']['ajax']
            && file_exists($locale_file)
        ) {
            unload_textdomain('recently');
            load_textdomain('recently', WP_LANG_DIR . '/plugins/recently-' . get_locale() . '.mo');
        }
    }
}
