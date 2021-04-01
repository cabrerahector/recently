<?php
namespace Recently\REST;

use Recently\Query;

abstract class Endpoint extends \WP_REST_Controller
{

    /**
     * Plugin options.
     *
     * @var     array      $config
     * @since   3.0.1
     * @access  private
     */
    protected $config;

    /**
     * Translate object.
     *
     * @var     \Recently\Translate    $translate
     * @since   3.0.1
     * @access  private
     */
    protected $translate;

    /**
     * Initializes class.
     *
     * @since   3.0.1
     * @param   array
     * @param   \Recently\Translate
     * @param   \Recently\Output
     */
    public function __construct(array $config, \Recently\Translate $translate)
    {
        $this->config = $config;
        $this->translate = $translate;
    }

    /**
     * Registers the endpoint(s).
     *
     * @since   3.0.1
     */
    abstract public function register();

    /**
     * Sets language/locale.
     *
     * @since   3.0.1
     */
    protected function set_lang($lang, &$args)
    {
        // Multilang support
        if ( $lang ) {
            $current_locale = get_locale();
            $locale = null;

            // Polylang support
            if ( function_exists('PLL') ) {
                $lang_object = PLL()->model->get_language($lang);
                $locale = ( $lang_object && isset($lang_object->locale) ) ? $lang_object->locale : null;
                $args['lang'] = $lang;
            } else {
                // WPML support
                global $sitepress;

                if ( is_object($sitepress) && method_exists($sitepress, 'get_locale_from_language_code') ) {
                    do_action('wpml_switch_language', $lang);
                    $locale = $sitepress->get_locale_from_language_code($lang);
                    $args['suppress_filters'] = false;
                }
            }

            // Reload locale if needed
            if ( $locale && $locale != $current_locale ) {
                $this->translate->set_current_language($lang);
                unload_textdomain('recently');
                load_textdomain('recently', WP_LANG_DIR . '/plugins/recently-' . $locale . '.mo');
            }
        }
    }
}
