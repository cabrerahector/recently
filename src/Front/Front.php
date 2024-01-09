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

use Recently\{ Helper, Translate };

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
     * Initialize the class and set its properties.
     *
     * @since   2.0.0
     * @param   array  $config
     * @param   \WordPressPopularPosts\Translate $translate
     */
    public function __construct(array $config, Translate $translate)
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
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_head', [$this, 'enqueue_scripts'], 1);
        add_action('wp_head', [$this, 'inline_loading_css']);
    }

    /**
     * Enqueues public facing styles.
     *
     * @since   3.0.0
     */
    public function enqueue_styles()
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
    }

    /**
     * Enqueues public facing scripts.
     *
     * @since   4.0.3
     */
    public function enqueue_scripts()
    {
        $recently_js_url = plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/front/js/recently.' . (! defined('WP_DEBUG') || false === WP_DEBUG ? 'min.' : '') . 'js';
        $is_single = Helper::is_single();

        wp_print_script_tag(
            [
                'id' => 'recently-js',
                'src' => $recently_js_url,
                'defer' => true,
                'data-api-url' => esc_url_raw(rest_url('recently')),
                'data-post-id' => (int) $is_single,
                'data-lang' => $this->translate->get_current_language()
            ]
        );
    }

    /**
     * CSS rules for the loading animation.
     *
     * @since   3.0.0
     */
    public function inline_loading_css()
    {
        $recently_insert_loading_animation_styles = apply_filters('recently_insert_loading_animation_styles', true);

        if ( $recently_insert_loading_animation_styles ) :
            ?>
            <style id="recently-loading-animation-styles">@-webkit-keyframes bgslide{from{background-position-x:0}to{background-position-x:-200%}}@keyframes bgslide{from{background-position-x:0}to{background-position-x:-200%}}.recently-widget-block-placeholder,.recently-widget-placeholder{margin:0 auto;width:60px;height:3px;background:#dd3737;background:-webkit-gradient(linear, left top, right top, from(#ffffff), color-stop(10%, #57b078), to(#ffffff));background:linear-gradient(90deg, #ffffff 0%, #57b078 10%, #ffffff 100%);background-size:200% auto;border-radius:3px;-webkit-animation:bgslide 1s infinite linear;animation:bgslide 1s infinite linear}</style>
            <?php
        endif;
    }
}
