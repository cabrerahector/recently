<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cabrerahector.com/
 * @since      2.0.0
 *
 * @package    Recently
 * @subpackage Recently/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks to
 * enqueue the public-specific stylesheet and JavaScript.
 *
 * @package    Recently
 * @subpackage Recently/public
 * @author     Hector Cabrera <me@cabrerahector.com>
 */
class Recently_Public {

    /**
     * The ID of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Administrative settings.
     *
     * @since	2.0.0
     * @var		array
     */
    private $admin_options = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    4.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->admin_options = Recently_Settings::get( 'admin_options' );

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_styles() {
        if ( $this->admin_options['tools']['misc']['include_stylesheet'] ) {
            $theme_file = get_stylesheet_directory() . '/recently.css';

            if ( @is_file( $theme_file ) ) {
                wp_enqueue_style( 'recently-css', get_stylesheet_directory_uri() . "/recently.css", array(), $this->version, 'all' );
            } // Load stock stylesheet
            else {
                wp_enqueue_style( 'recently-css', plugin_dir_url( __FILE__ ) . 'css/recently.css', array(), $this->version, 'all' );
            }
        }
    }

    /**
     * Register the scripts for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts() {
        wp_register_script( 'recently-js', plugin_dir_url( __FILE__ ) . 'js/recently.min.js', array(), $this->version, true );
        wp_enqueue_script( 'recently-js' );
    }

} // End Recently_Public class
