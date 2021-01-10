<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cabrerahector.com/
 * @since      2.0.0
 *
 * @package    Recently
 * @subpackage Recently/Front
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks to
 * enqueue the public-specific stylesheet and JavaScript.
 *
 * @package    Recently
 * @subpackage Recently/Front
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Front;

use Recently\Helper;

class Front {

    /**
     * Administrative settings.
     *
     * @since   2.0.0
     * @var     array
     */
    private $admin_options = array();

    /**
     * Translate object.
     *
     * @since   3.0.0
     * @var     \Recently\Translate    $translate
     */
    private $translate;

    /**
     * Output object.
     *
     * @since   3.0.0
     * @var     \Recently\Output       $output
     */
    private $output;

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.0.0
     * @param   array  $config
     * @param   \WordPressPopularPosts\Translate $translate
     */
    public function __construct(array $config, \Recently\Translate $translate)
    {
        $this->admin_options = $config;
        $this->translate = $translate;
    }

    /**
     * WordPress public-facing hooks.
     *
     * @since   3.0.0
     */
    public function hooks()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_filter('script_loader_tag', [$this, 'convert_inline_js_into_json'], 10, 3);
        add_action('wp_head', [$this, 'inline_loading_css']);
    }

    /**
     * Enqueues public facing assets.
     *
     * @since   3.0.0
     */
    public function enqueue_assets()
    {
        $plugin_dir_url = plugin_dir_url(dirname(dirname(__FILE__)));

        if ( $this->admin_options['tools']['misc']['include_stylesheet'] ) {
            $theme_file = get_stylesheet_directory() . '/recently.css';

            if ( @is_file($theme_file) ) {
                wp_enqueue_style('recently-css', get_stylesheet_directory_uri() . "/recently.css", array(), RECENTLY_VERSION, 'all');
            } // Load stock stylesheet
            else {
                wp_enqueue_style('recently-css', $plugin_dir_url . 'assets/front/css/recently.css', array(), RECENTLY_VERSION, 'all');
            }
        }

        $is_single = Helper::is_single();

        wp_register_script('recently-js', $plugin_dir_url . 'assets/front/js/recently.min.js', array(), RECENTLY_VERSION, true);
        $params = [
            'ajax_url' => esc_url_raw(rest_url('recently/v1')),
            'ID' => (int) $is_single,
            'lang' => function_exists('PLL') ? $this->translate->get_current_language() : 0
        ];
        wp_enqueue_script('recently-js');
        wp_add_inline_script('recently-js', json_encode($params), 'before');
    }

    /**
     * CSS rules for the loading animation.
     *
     * @since   3.0.0
     */
    public function inline_loading_css()
    {
        ?>
        <style>
            @-webkit-keyframes bgslide {
                from {
                    background-position-x: 0;
                }
                to {
                    background-position-x: -200%;
                }
            }

            @keyframes bgslide {
                    from {
                        background-position-x: 0;
                    }
                    to {
                        background-position-x: -200%;
                    }
            }

            .recently-widget-placeholder {
                margin: 0 auto;
                width: 60px;
                height: 3px;
                background: #dd3737;
                background: -webkit-gradient(linear, left top, right top, from(#ffffff), color-stop(10%, #57b078), to(#ffffff));
                background: linear-gradient(90deg, #ffffff 0%, #57b078 10%, #ffffff 100%);
                background-size: 200% auto;
                border-radius: 3px;
                -webkit-animation: bgslide 1s infinite linear;
                animation: bgslide 1s infinite linear;
            }
        </style>
        <?php
    }

    /**
     * Converts inline script tag into type=application/json.
     *
     * This function mods the original script tag as printed
     * by WordPress which contains the data for the wpp_params
     * object into a JSON script. This improves compatibility
     * with Content Security Policy (CSP).
     *
     * @since   3.0.0
     * @param   string  $tag
     * @param   string  $handle
     * @param   string  $src
     * @return  string  $tag
     */
    function convert_inline_js_into_json($tag, $handle, $src)
    {
        if ( 'recently-js' === $handle ) {
            // id attribute found, replace it
            if ( false !== strpos($tag, 'recently-js-js-before') ) {
                $tag = str_replace('recently-js-js-before', 'recently-json', $tag);
            } // id attribute missing, let's add it
            else {
                $pos = strpos($tag, '>');
                $tag = substr_replace($tag, ' id="recently-json">', $pos, 1);
            }

            // type attribute found, replace it
            if ( false !== strpos($tag, 'type') ) {
                $pos = strpos($tag, 'text/javascript');

                if ( false !== $pos )
                    $tag = substr_replace($tag, 'application/json', $pos, strlen('text/javascript'));
            } // type attribute missing, let's add it
            else {
                $pos = strpos($tag, '>');
                $tag = substr_replace($tag, ' type="application/json">', $pos, 1);
            }
        }

        return $tag;
    }
}
