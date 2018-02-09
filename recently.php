<?php
/*
Plugin Name: Recently
Plugin URI: http://wordpress.org/extend/plugins/recently
Description: Recently is a highly customizable widget that displays your most recent posts
Version: 1.0.2
Author: Hector Cabrera
Author URI: http://cabrerahector.com
Author Email: me@cabrerahector.com
Text Domain: recently
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2015-2018 Hector Cabrera (me@cabrerahector.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined('ABSPATH') )
    exit('Please do not load this file directly.');

/**
 * Recently class.
 */
if ( !class_exists('Recently') ) {

    /**
     * Register plugin's activation / deactivation functions
     * @since	1.0.0
     */
    register_activation_hook( __FILE__, array( 'Recently', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'Recently', 'deactivate' ) );

    /**
     * Add function to widgets_init that'll load Recently.
     * @since	1.0.0
     */
    function load_recently() {
        register_widget( 'Recently' );
    }
    add_action( 'widgets_init', 'load_recently' );

    class Recently extends WP_Widget {

        /**
         * Plugin version, used for cache-busting of style and script file references.
         *
         * @since	1.0.0
         * @var		string
         */
        private $version = '1.0.2';

        /**
         * Plugin identifier.
         *
         * @since	1.0.0
         * @var		string
         */
        private $plugin_slug = 'recently';

        /**
         * Instance of this class.
         *
         * @since    1.0.0
         * @var      object
         */
        protected static $instance = NULL;

        /**
         * Slug of the plugin screen.
         *
         * @since	1.0.0
         * @var		string
         */
        protected $plugin_screen_hook_suffix = NULL;

        /**
         * Flag for singular pages.
         *
         * @since	1.0.0
         * @var		int
         */
        private $current_post_id = 0;

        /**
         * Plugin directory.
         *
         * @since	1.0.0
         * @var		string
         */
        private $plugin_dir = '';

        /**
         * Plugin uploads directory.
         *
         * @since	1.0.0
         * @var		array
         */
        private $uploads_dir = array();

        /**
         * Default thumbnail.
         *
         * @since	1.0.0
         * @var		string
         */
        private $default_thumbnail = '';

        /**
         * Default thumbnail sizes
         *
         * @since	1.0.0
         * @var		array
         */
        private $default_thumbnail_sizes = array();

        /**
         * Flag to verify if thumbnails can be created or not.
         *
         * @since	1.0.0
         * @var		bool
         */
        private $thumbs = false;

        /**
         * Default charset.
         *
         * @since	1.0.0
         * @var		string
         */
        private $charset = "UTF-8";

        /**
         * Plugin defaults.
         *
         * @since	1.0.0
         * @var		array
         */
        protected $defaults = array(
            'args' => array(
                'post_type' => array('post'),
                'post_status' => 'publish',
                'posts_per_page' => 10,
                'offset' => 0,
                'ignore_sticky_posts' => true
            ),
            'title' => '',
            'shorten_title' => array(
                'active' => false,
                'length' => 25,
                'words'	=> false
            ),
            'post-excerpt' => array(
                'active' => false,
                'length' => 55,
                'keep_format' => false,
                'words' => false
            ),
            'thumbnail' => array(
                'active' => false,
                'build' => 'manual',
                'width' => 50,
                'height' => 50,
                'crop' => true
            ),
            'rating' => false,
            'meta_tag' => array(
                'comment_count' => true,
                'views' => false,
                'author' => false,
                'date' => array(
                    'active' => false,
                    'format' => 'F j, Y'
                ),
                'taxonomy' =>  array(
                    'active' => false,
                    'names' => array()
                )
            ),
            'markup' => array(
                'custom_html' => false,
                'recently-start' => '<ul class="recently-list">',
                'recently-end' => '</ul>',
                'post-html' => '<li>{thumb} {title} {metadata}</li>',
                'post-start' => '<li>',
                'post-end' => '</li>',
                'title-start' => '<h2>',
                'title-end' => '</h2>'
            )
        );

        /**
         * Admin page user settings defaults.
         *
         * @since	1.0.0
         * @var		array
         */
        protected $default_user_settings = array(
            'tools' => array(
                'markup' => array(
                    'link' => array(
                        'attr' => array(
                            'rel' => 'bookmark',
                            'target' => '_self'
                        )
                    ),
                    'thumbnail' => array(
                        'source' => 'featured',
                        'field' => '',
                        'resize' => false,
                        'default' => ''
                    ),
                ),
                'data' => array(
                    'ajax' => false,
                    'cache' => array(
                        'active' => false,
                        'interval' => array(
                            'time' => 'hour',
                            'value' => 1
                        )
                    )
                ),
                'misc' => array(
                    'include_stylesheet' => true
                )
            )
        );

        /**
         * Admin page user settings.
         *
         * @since	1.0.0
         * @var		array
         */
        private $user_settings = array();

        /*--------------------------------------------------*/
        /* Constructor
        /*--------------------------------------------------*/

        /**
         * Initialize the widget by setting localization, filters, and administration functions.
         *
         * @since	1.0.0
         */
        public function __construct() {

            // Load plugin text domain
            add_action( 'init', array( $this, 'widget_textdomain' ) );

            // Upgrade check
            add_action( 'init', array( $this, 'upgrade_check' ) );

            // Check location on template redirect
            add_action( 'template_redirect',  array( $this, 'is_single' ) );

            // Hook fired when a new blog is activated on WP Multisite
            add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

            // Notices check
            add_action( 'admin_notices', array( $this, 'check_admin_notices' ) );

            // Create the widget
            parent::__construct(
                'recently',
                'Recently',
                array(
                    'classname'		=>	'recently',
                    'description'	=>	__( 'Your site\'s most recent posts.', $this->plugin_slug )
                )
            );

            // Get user options
            $this->user_settings = get_site_option('recently_config');
            if ( !$this->user_settings ) {
                add_site_option('recently_config', $this->default_user_settings);
                $this->user_settings = $this->default_user_settings;
            } else {
                $this->user_settings = $this->merge_array_r( $this->default_user_settings, $this->user_settings );
            }

            // Add the options page and menu item.
            add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

            // Register admin styles and scripts
            add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
            add_action( 'admin_init', array( $this, 'thickbox_setup' ) );

            // Register site styles and scripts
            if ( $this->user_settings['tools']['misc']['include_stylesheet'] )
                add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

            // Add plugin settings link
            add_filter( 'plugin_action_links', array( $this, 'add_plugin_settings_link' ), 10, 2 );

            // Set plugin directory
            $this->plugin_dir = plugin_dir_url(__FILE__);

            // Get blog charset
            $this->charset = get_bloginfo('charset');

            // Add thumbnail cache truncation to wp_ajax_ hook
            add_action('wp_ajax_recently_clear_thumbnail', array( $this, 'clear_thumbnails' ));

            // Add ajax hook for widget
            add_action('wp_ajax_get_recently', array( $this, 'get_recently') );
            add_action('wp_ajax_nopriv_get_recently', array( $this, 'get_recently') );

            // Check if images can be created
            if ( extension_loaded('ImageMagick') || (extension_loaded('GD') && function_exists('gd_info')) ) {
                // Enable thumbnail feature
                $this->thumbs = true;

                // Get available thumbnail size(s)
                $this->default_thumbnail_sizes = $this->get_image_sizes();

                // Set uploads folder
                $wp_upload_dir = wp_upload_dir();
                $this->uploads_dir['basedir'] = $wp_upload_dir['basedir'] . "/" . $this->plugin_slug;
                $this->uploads_dir['baseurl'] = $wp_upload_dir['baseurl'] . "/" . $this->plugin_slug;

                if ( !is_dir($this->uploads_dir['basedir']) ) {
                    if ( !wp_mkdir_p($this->uploads_dir['basedir']) ) {
                        $this->uploads_dir['basedir'] = $wp_upload_dir['basedir'];
                        $this->uploads_dir['baseurl'] = $wp_upload_dir['baseurl'];
                    }
                }
            }

            // Set default thumbnail
            $this->default_thumbnail = $this->plugin_dir . "no_thumb.jpg";
            $this->default_user_settings['tools']['markup']['thumbnail']['default'] = $this->default_thumbnail;

            if ( !empty($this->user_settings['tools']['markup']['thumbnail']['default']) )
                $this->default_thumbnail = $this->user_settings['tools']['markup']['thumbnail']['default'];
            else
                $this->user_settings['tools']['markup']['thumbnail']['default'] = $this->default_thumbnail;

        } // end constructor

        /*--------------------------------------------------*/
        /* Widget API Functions
        /*--------------------------------------------------*/

        /**
         * Outputs the content of the widget.
         *
         * @since	1.0.0
         * @param	array	args		The array of form elements
         * @param	array	instance	The current instance of the widget
         */
        public function widget( $args, $instance ) {
            /**
             * @var String $name
             * @var String $id
             * @var String $description
             * @var String $class
             * @var String $before_widget
             * @var String $after_widget
             * @var String $before_title
             * @var String $after_title
             * @var String $widget_id
             * @var String $widget_name
             */
            extract( $args, EXTR_SKIP );

            $markup = ( $instance['markup']['custom_html'] || has_filter('recently_custom_html') )
              ? 'custom'
              : 'regular';

            echo "\n". "<!-- Recently widget v{$this->version} [{$markup}] -->" . "\n";

            echo $before_widget . "\n";

            $title = ( '' != $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : 'Recently';

            if ( $instance['markup']['custom_html'] ) {
                echo htmlspecialchars_decode($instance['markup']['title-start'], ENT_QUOTES) . $title . htmlspecialchars_decode($instance['markup']['title-end'], ENT_QUOTES);
            } else {
                echo $before_title . $title . $after_title;
            }

            if ( $this->user_settings['tools']['data']['ajax'] ) {
                if ( empty($before_widget) || !preg_match('/id="[^"]*"/', $before_widget) ) {
                ?>
                <p><?php printf( __('Error: cannot ajaxify Recently on this theme. It\'s missing the <em>id</em> attribute on <em>before_widget</em> (see <a href="%s" target="_blank" rel="external nofollow">register_sidebar</a> for more)', $this->plugin_slug ), 'https://codex.wordpress.org/Function_Reference/register_sidebar' ); ?>.</p>
                <?php
                } else {
                ?>
                <script type="text/javascript">//<![CDATA[
                    // jQuery is available, so proceed
                    if ( window.jQuery ) {

                        jQuery(document).ready(function($){
                            $.get('<?php echo admin_url('admin-ajax.php'); ?>', {
                                action: 'get_recently',
                                widget_id: '<?php echo $this->number; ?>'
                            }, function(data){
                                $('#<?php echo $widget_id; ?>').append(data);
                            });
                        });

                    } else { // jQuery is not defined
                        if ( window.console && window.console.log )
                            window.console.log('Recently: jQuery is not defined!');
                    }
                //]]></script>
                <?php
                }
            } else {
                echo $this->get_recents( $instance );
            }

            echo $after_widget . "\n";
            echo "<!-- End Recently Plugin v{$this->version} -->" . "\n";

        } // end widget

        /**
         * Processes the widget's options to be saved.
         *
         * @since	1.0.0
         * @param	array	new_instance	The previous instance of values before the update.
         * @param	array	old_instance	The new instance of values to be generated via the update.
         * @return	array	instance		Updated instance.
         */
        public function update( $new_instance, $old_instance ) {

            $instance = $old_instance;

            // Widget title
            $instance['title'] = apply_filters( 'widget_title', $new_instance['title'] );

            /*
             * Query args
             */
            // Post type
            $instance['args']['post_type'] = ( '' == $new_instance['post_type'] )
              ? array('post')
              : explode(",", preg_replace( '|[^a-z0-9,_-]|', '', $new_instance['post_type'] ));

            // Post per page
            $instance['args']['posts_per_page'] = ( $this->is_numeric($new_instance['posts_per_page']) && $new_instance['posts_per_page'] > 0 )
              ? $new_instance['posts_per_page']
              : 10;

            // Offset
            $instance['args']['offset'] = ( $this->is_numeric($new_instance['offset']) && $new_instance['offset'] >= 0 )
              ? $new_instance['offset']
              : 0;

            // OPTIONAL ARGS
            // Post IDs
            $new_instance['post_id'] = preg_replace( '|[^0-9,]|', '', $new_instance['post_id'] );

            if ( !empty($new_instance['post_id']) ) {
                $instance['args']['post__not_in'] = explode(",", $new_instance['post_id']);
            } else {
                if ( isset($instance['args']['post__not_in']) )
                    unset( $instance['args']['post__not_in'] );
            }

            // Taxonomy
            if ( isset($instance['args']['tax_query']) ) // reset
                unset( $instance['args']['tax_query'] );

            if ( isset($new_instance['taxonomy']) && -1 != $new_instance['taxonomy'] ) {

                // Filter by Post Format
                if ( 'post_format' == $new_instance['taxonomy'] ) {

                    $tax_slugs = trim( preg_replace('|[^a-z0-9,-]|', '', $new_instance['tax_slug']), "," );

                    if ( !empty($tax_slugs) ) {

                        $instance['args']['tax_query'][] = array(
                            'taxonomy' => $new_instance['taxonomy'],
                            'field' => 'slug',
                            'terms' => explode(",", $tax_slugs)
                        );

                    }

                } // Filter by taxonomy ID
                else {

                    // Taxonomy IDs
                    $new_instance['tax_id'] = trim( preg_replace('|[^0-9,-]|', '', $new_instance['tax_id']), "," );

                    if ( '' != $new_instance['tax_id'] ) {

                        $tax_ids = explode(",", $new_instance['tax_id']);
                        $tax_ids_in = array();
                        $tax_ids_out = array();

                        for ( $i=0; $i < count($tax_ids); $i++ ) {
                            if ($tax_ids[$i] >= 0)
                                $tax_ids_in[] = $tax_ids[$i];
                            else
                                $tax_ids_out[] = $tax_ids[$i];
                        }

                        if ( !empty($tax_ids_in) && !empty($tax_ids_out) )
                            $instance['args']['tax_query']['relation'] = 'AND';

                        if ( !empty($tax_ids_in) ) {
                            $instance['args']['tax_query'][] = array(
                                'taxonomy' => $new_instance['taxonomy'],
                                'field' => 'term_id',
                                'terms' => $tax_ids_in
                            );
                        }

                        if ( !empty($tax_ids_out) ) {
                            // remove minus sign
                            foreach ($tax_ids_out as &$value)
                                $value *= (-1);

                            $instance['args']['tax_query'][] = array(
                                'taxonomy' => $new_instance['taxonomy'],
                                'field' => 'term_id',
                                'terms' => $tax_ids_out,
                                'operator' => 'NOT IN'
                            );
                        }

                    }

                }

            }

            // Author IDs
            $new_instance['author_id'] = trim( preg_replace('|[^0-9,-]|', '', $new_instance['author_id']), "," );

            if ( !empty($new_instance['author_id']) ) {

                $author_ids = explode(",", $new_instance['author_id']);
                $author_ids_in = array();
                $author_ids_out = array();

                for ( $i=0; $i < count($author_ids); $i++ ) {
                    if ($author_ids[$i] >= 0)
                        $author_ids_in[] = $author_ids[$i];
                    else
                        $author_ids_out[] = $author_ids[$i];
                }

                if ( !empty($author_ids_out) ) {
                    // remove minus sign
                    foreach ($author_ids_out as &$value)
                        $value *= (-1);
                }

                $instance['args']['author__in'] = $author_ids_in;
                $instance['args']['author__not_in'] = $author_ids_out;

            } else {
                if ( isset($instance['args']['author__in']) )
                    unset( $instance['args']['author__in'] );

                if ( isset($instance['args']['author__not_in']) )
                    unset( $instance['args']['author__not_in'] );
            }

            /*
             * Formatting options
             */
            // post title
            $instance['shorten_title']['active'] = isset( $new_instance['shorten_title-active'] );
            $instance['shorten_title']['words'] = $new_instance['shorten_title-words'];
            $instance['shorten_title']['length'] = ( $this->is_numeric($new_instance['shorten_title-length']) && $new_instance['shorten_title-length'] > 0 )
              ? $new_instance['shorten_title-length']
              : 25;

            // post excerpt
            $instance['post-excerpt']['keep_format'] = isset( $new_instance['post-excerpt-format'] );
            $instance['post-excerpt']['words'] = $new_instance['post-excerpt-words'];
            $instance['post-excerpt']['active'] = isset( $new_instance['post-excerpt-active'] );
            $instance['post-excerpt']['length'] = ( $this->is_numeric($new_instance['post-excerpt-length']) && $new_instance['post-excerpt-length'] > 0 )
              ? $new_instance['post-excerpt-length']
              : 55;

            // post thumbnail
            $instance['thumbnail']['active'] = false;
            $instance['thumbnail']['width'] = 75;
            $instance['thumbnail']['height'] = 75;

            if ( $this->thumbs ) {

                $instance['thumbnail']['active'] = isset( $new_instance['thumbnail-active'] );
                $instance['thumbnail']['build'] = $new_instance['thumbnail-size-source'];

                // Use predefined thumbnail sizes
                if ( 'predefined' == $new_instance['thumbnail-size-source'] ) {
                    $size = $this->default_thumbnail_sizes[ $new_instance['thumbnail-size'] ];
                    $instance['thumbnail']['width'] = $size['width'];
                    $instance['thumbnail']['height'] = $size['height'];
                    $instance['thumbnail']['crop'] = $size['crop'];
                } // Set thumbnail size manually
                else {
                    if ($this->is_numeric($new_instance['thumbnail-width']) && $this->is_numeric($new_instance['thumbnail-height'])) {
                        $instance['thumbnail']['width'] = $new_instance['thumbnail-width'];
                        $instance['thumbnail']['height'] = $new_instance['thumbnail-height'];
                        $instance['thumbnail']['crop'] = true;
                    }
                }

            }

            // WP Post Rating support
            $instance['rating'] = isset( $new_instance['rating'] );

            // Statistics
            $instance['meta_tag']['comment_count'] = isset( $new_instance['comment_count'] );
            $instance['meta_tag']['views'] = isset( $new_instance['views'] );
            $instance['meta_tag']['author'] = isset( $new_instance['author'] );
            $instance['meta_tag']['date']['active'] = isset( $new_instance['date'] );
            $instance['meta_tag']['date']['format'] = empty($new_instance['date_format'])
              ? 'F j, Y'
              : $new_instance['date_format'];
            $instance['meta_tag']['taxonomy']['active'] = isset( $new_instance['show_tax'] );
            $instance['meta_tag']['taxonomy']['names'] = ( !empty($new_instance['taxonomy_name']) && isset( $new_instance['show_tax'] ) ) ? $new_instance['taxonomy_name'] : array();

            // Custom HTML
            $instance['markup']['custom_html'] = isset( $new_instance['custom_html'] );
            $instance['markup']['recently-start'] = ( !isset($new_instance['recently-start']) || empty($new_instance['recently-start']) )
              ? '<ul class="recently-list">'
              : htmlspecialchars_decode( $new_instance['recently-start'], ENT_QUOTES );

            $instance['markup']['recently-end'] = ( !isset($new_instance['recently-end']) || empty($new_instance['recently-end']) )
              ? '</ul>'
              : htmlspecialchars_decode( $new_instance['recently-end'], ENT_QUOTES );

            $instance['markup']['post-html'] = empty($new_instance['post-html'])
              ? '<li>{thumb} {title} {metadata}</li>'
              : htmlspecialchars_decode( $new_instance['post-html'], ENT_QUOTES );

            $instance['markup']['title-start'] = !isset($new_instance['title-start'])
              ? '<h2>'
              : htmlspecialchars_decode( $new_instance['title-start'], ENT_QUOTES );

            $instance['markup']['title-end'] = !isset($new_instance['title-end'])
              ? '</h2>'
              : htmlspecialchars_decode( $new_instance['title-end'], ENT_QUOTES );

            return $instance;

        } // end widget

        /**
         * Generates the administration form for the widget.
         *
         * @since	1.0.0
         * @param	array	instance	The array of keys and values for the widget.
         */
        public function form( $instance ) {

            // parse instance values
            $instance = $this->merge_array_r(
                $this->defaults,
                $instance
            );

            // Display the admin form
            include( plugin_dir_path(__FILE__) . '/views/form.php' );

        } // end form

        /*--------------------------------------------------*/
        /* Public methods
        /*--------------------------------------------------*/

        /**
         * Return the widget slug.
         *
         * @since	1.0.0
         *
         * @return	string	Plugin slug variable.
         */
        public function get_widget_slug() {
            return $this->plugin_slug;
        }

        /**
         * Loads the Widget's text domain for localization and translation.
         *
         * @since	1.0.0
         */
        public function widget_textdomain() {

            $domain = $this->plugin_slug;
            $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

            load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
            load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

        } // end widget_textdomain

        /**
         * Registers and enqueues admin-specific styles.
         *
         * @since	1.0.0
         */
        public function register_admin_styles() {

            if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
                return;
            }

            $screen = get_current_screen();
            if ( $screen->id == $this->plugin_screen_hook_suffix ) {
                wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'style/admin.css', __FILE__ ), array(), $this->version );
            }

        } // end register_admin_styles

        /**
         * Registers and enqueues admin-specific JavaScript.
         *
         * @since	1.0.0
         */
        public function register_admin_scripts( $hook ) {

            if ( 'widgets.php' == $hook )
                wp_enqueue_script( $this->get_widget_slug().'-widget-admin', plugins_url( 'js/widget-admin.js', __FILE__ ), array('jquery'), false, true );

            if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
                return;
            }

            $screen = get_current_screen();
            if ( $screen->id == $this->plugin_screen_hook_suffix ) {
                wp_enqueue_script( 'thickbox' );
                wp_enqueue_style( 'thickbox' );
                wp_enqueue_script( 'media-upload' );
                wp_enqueue_script( $this->plugin_slug .'-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery'), $this->version );
            }

        } // end register_admin_scripts

        /**
         * Hooks into getttext to change upload button text when uploader is called by Recently.
         *
         * @since	1.0.0
         */
        function thickbox_setup() {

            global $pagenow;
            if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
                add_filter( 'gettext', array( $this, 'replace_thickbox_text' ), 1, 3 );
            }

        } // end thickbox_setup

        /**
         * Replaces upload button text when uploader is called by Recently.
         *
         * @since	1.0.0
         * @param	string	translated_text
         * @param	string	text
         * @param	string	domain
         * @return	string
         */
        function replace_thickbox_text($translated_text, $text, $domain) {

            if ('Insert into Post' == $text) {
                $referer = strpos( wp_get_referer(), 'recently' );
                if ( $referer != '' ) {
                    return __('Upload', $this->plugin_slug );
                }
            }

            return $translated_text;

        } // end replace_thickbox_text

        /**
         * Registers and enqueues widget-specific styles.
         *
         * @since	1.0.0
         */
        public function register_widget_styles() {

            $theme_file = get_stylesheet_directory() . '/recently.css';
            $plugin_file = plugin_dir_path(__FILE__) . 'style/recently.css';

            if ( @file_exists($theme_file) ) { // user stored a recently.css file in theme's directory, so use it
                wp_enqueue_style( $this->plugin_slug, get_stylesheet_directory_uri() . "/recently.css", array(), $this->version );
            } elseif ( @file_exists($plugin_file) ) { // no custom recently.css, use plugin's instead
                wp_enqueue_style( $this->plugin_slug, plugins_url( 'style/recently.css', __FILE__ ), array(), $this->version );
            }

        } // end register_widget_styles

        /**
         * Registers and enqueues widget-specific scripts.
         *
         * @since	1.0.0
         */
        public function register_widget_scripts() {
            // We need jQuery in the front-end only when ajaxifying the widget
            if ( $this->user_settings['tools']['data']['ajax'] )
                wp_enqueue_script( 'jquery' );
        } // end register_widget_scripts

        /**
         * Register the administration menu for this plugin into the WordPress Dashboard menu.
         *
         * @since    1.0.0
         */
        public function add_plugin_admin_menu() {

            $this->plugin_screen_hook_suffix = add_options_page(
                'Recently',
                'Recently',
                'manage_options',
                $this->plugin_slug,
                array( $this, 'display_plugin_admin_page' )
            );

        }

        /**
         * Render the settings page for this plugin.
         *
         * @since    1.0.0
         */
        public function display_plugin_admin_page() {
            include_once( 'views/admin.php' );
        }

        /**
         * Registers Settings link on plugin description.
         *
         * @since	1.0.0
         * @param	array	links
         * @param	string	file
         * @return	array
         */
        public function add_plugin_settings_link( $links, $file ){

            $this_plugin = plugin_basename(__FILE__);

            if ( is_plugin_active($this_plugin) && $file == $this_plugin ) {
                $links[] = '<a href="' . admin_url( 'options-general.php?page=recently' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>';
            }

            return $links;

        } // end add_plugin_settings_link

        /*--------------------------------------------------*/
        /* Install / activation / deactivation methods
        /*--------------------------------------------------*/

        /**
         * Return an instance of this class.
         *
         * @since     1.0.0
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

            // If the single instance hasn't been set, set it now.
            if ( NULL == self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;

        } // end get_instance

        /**
         * Fired when the plugin is activated.
         *
         * @since	1.0.0
         * @global	object	wpdb
         * @param	bool	network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
         */
        public static function activate( $network_wide ) {

            global $wpdb;

            if ( function_exists( 'is_multisite' ) && is_multisite() ) {

                // run activation for each blog in the network
                if ( $network_wide ) {

                    $original_blog_id = get_current_blog_id();
                    $blogs_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

                    foreach( $blogs_ids as $blog_id ) {
                        switch_to_blog( $blog_id );
                        self::activate_site();
                    }

                    // switch back to current blog
                    switch_to_blog( $original_blog_id );

                    return;

                }

            }

            self::activate_site();

        } // end activate

        /**
         * Fired when a new blog is activated on WP Multisite.
         *
         * @since	1.0.0
         * @param	int	blog_id	New blog ID
         */
        public function activate_new_site( $blog_id ){

            if ( 1 !== did_action( 'wpmu_new_blog' ) )
                return;

            // run activation for the new blog
            switch_to_blog( $blog_id );
            self::activate_site();

            // switch back to current blog
            restore_current_blog();

        } // end activate_new_site

        /**
         * Run checks on plugin activation.
         *
         * @since	1.0.0
         * @global	object	wpdb
         */
        private static function activate_site() {

            // TODO

        } // end activate_site

        /**
         * Fired when the plugin is deactivated.
         *
         * @since	1.0.0
         * @global	object	wpbd
         * @param	bool	network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
         */
        public static function deactivate( $network_wide ) {

            global $wpdb;

            if ( function_exists( 'is_multisite' ) && is_multisite() ) {

                // Run deactivation for each blog in the network
                if ( $network_wide ) {

                    $original_blog_id = get_current_blog_id();
                    $blogs_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

                    foreach( $blogs_ids as $blog_id ) {
                        switch_to_blog( $blog_id );
                        self::deactivate_site();
                    }

                    // Switch back to current blog
                    switch_to_blog( $original_blog_id );

                    return;

                }

            }

            self::deactivate_site();

        } // end deactivate

        /**
         * On plugin deactivation, disables the shortcode and removes the scheduled task.
         *
         * @since	1.0.0
         */
        private static function deactivate_site() {
            // TODO
        } // end deactivate_site

        /**
         * Checks if an upgrade procedure is required.
         *
         * @since	1.0.0
         */
        public function upgrade_check(){

            // Get Recently version
            $recently_ver = get_site_option('recently_ver');

            if ( !$recently_ver ) {
                add_site_option('recently_ver', $this->version);
            } elseif ( version_compare($recently_ver, $this->version, '<') ) {
                $this->upgrade();
            }

        } // end upgrade_check

        /**
         * On plugin upgrade, performs a number of actions.
         *
         * @since	1.0.0
         * @global	object	wpdb
         */
        private function upgrade() {
            // TODO

            // Update Recently version
            update_site_option('recent_options', $this->version);

        } // end upgrade

        /**
         * Checks if the technical requirements are met.
         *
         * @since	1.0.0
         * @link	http://wordpress.stackexchange.com/questions/25910/uninstall-activate-deactivate-a-plugin-typical-features-how-to/25979#25979
         * @global	string $wp_version
         * @return	array
         */
        private function check_requirements() {

            global $wp_version;

            $php_min_version = '5.2';
            $wp_min_version = '3.9';
            $php_current_version = phpversion();
            $errors = array();

            if ( version_compare( $php_min_version, $php_current_version, '>' ) ) {
                /* translators: the %1$s fragment outputs the version number */
                $errors[] = sprintf( __( 'Your PHP installation is too old. Recently requires at least PHP version %1$s to function correctly. Please contact your hosting provider and ask them to upgrade PHP to %1$s or higher.', $this->plugin_slug ),$php_min_version );
            }

            if ( version_compare( $wp_min_version, $wp_version, '>' ) ) {
                /* translators: the %1$s fragment outputs the version number */
                $errors[] = sprintf( __( 'Your WordPress version is too old. Recently requires at least WordPress version %1$s to function correctly. Please update your blog via Dashboard &gt; Update.', $this->plugin_slug ), $wp_min_version );
            }

            return $errors;

        } // end check_requirements

        /**
         * Outputs error messages to wp-admin.
         *
         * @since	1.0.0
         */
        public function check_admin_notices() {

            $errors = $this->check_requirements();

            if ( empty($errors) )
                return;

            if ( isset($_GET['activate']) )
                unset($_GET['activate']);

            /* translators: the %1$s fragment outputs the errors found during plugin activation, and %2$s the plugin name */
            printf( __('<div class="error"><p>%1$s</p><p><i>%2$s</i> has been <strong>deactivated</strong>.</p></div>', $this->plugin_slug), join( '</p><p>', $errors ), 'Recently'	);

            deactivate_plugins( plugin_basename( __FILE__ ) );

        } // end check_admin_notices


        /*--------------------------------------------------*/
        /* Plugin methods / functions
        /*--------------------------------------------------*/

        /**
         * Truncates thumbnails cache on demand.
         *
         * @since	1.0.0
         * @global	object	wpdb
         */
        public function clear_thumbnails() {

            $token = $_POST['token'];
            $key = get_site_option("recently_rand");

            if ( current_user_can('manage_options') && ($token === $key) ) {
                $wp_upload_dir = wp_upload_dir();

                if ( is_dir( $wp_upload_dir['basedir'] . "/" . $this->plugin_slug ) ) {
                    $files = glob( $wp_upload_dir['basedir'] . "/" . $this->plugin_slug . "/*" ); // get all file names

                    if ( is_array($files) && !empty($files) ) {
                        foreach($files as $file){ // iterate files
                            if ( is_file($file) )
                                @unlink($file); // delete file
                        }

                        _e('Success! All files have been deleted!', $this->plugin_slug);
                    } else {
                        _e('The thumbnail cache is already empty!', $this->plugin_slug);
                    }
                } else {
                    _e('Invalid action.', $this->plugin_slug);
                }
            } else {
                _e('Sorry, you do not have enough permissions to do this. Please contact the site administrator for support.', $this->plugin_slug);
            }

            die();

        } // end clear_data

        /**
         * Deletes cached (transient) data.
         *
         * @since	1.0.0
         */
        private function flush_transients() {

            $recently_transients = get_site_option('recently_transients');

            if ( $recently_transients && is_array($recently_transients) && !empty($recently_transients) ) {
                for ($t=0; $t < count($recently_transients); $t++)
                    delete_transient( $recently_transients[$t] );

                update_site_option('recently_transients', array());
            }

        } // end flush_transients

        /**
         * Queries the database and returns the posts (if any met the criteria set by the user).
         *
         * @since	1.0.0
         * @global	object 		$wpdb
         * @param	array		$instance	Widget instance
         * @return	null|array	Array of posts, or null if nothing was found
         */
        protected function _query_posts( $instance ) {

            global $wpdb;

            // parse instance values
            $instance = $this->merge_array_r(
                $this->defaults,
                $instance
            );

            $args = apply_filters( 'recently_query_args', $instance['args'] );

            return new WP_Query( $args );

        } // end query_posts

        /**
         * Returns the formatted list of posts.
         *
         * @since	1.0.0
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string	HTML list of recent posts
         */
        private function get_recents( $instance ) {

            // Parse instance values
            $instance = $this->merge_array_r(
                $this->defaults,
                $instance
            );

            $content = "";

            // Fetch posts
            if ( $this->user_settings['tools']['data']['cache']['active'] ) {
                $transient_name = md5(json_encode($instance));
                $recents = ( function_exists( 'is_multisite' ) && is_multisite() )
                  ? get_site_transient( $transient_name )
                  : get_transient( $transient_name );

                $content = "\n" . "<!-- cached -->" . "\n";

                // It wasn't there, so regenerate the data and save the transient
                if ( false === $recents ) {
                    $recents = $this->_query_posts( $instance );

                    switch( $this->user_settings['tools']['data']['cache']['interval']['time'] ){
                        case 'minute':
                            $time = 60;
                        break;

                        case 'hour':
                            $time = 60 * 60;
                        break;

                        case 'day':
                            $time = 60 * 60 * 24;
                        break;

                        case 'week':
                            $time = 60 * 60 * 24 * 7;
                        break;

                        case 'month':
                            $time = 60 * 60 * 24 * 30;
                        break;

                        case 'year':
                            $time = 60 * 60 * 24 * 365;
                        break;
                    }

                    $expiration = $time * $this->user_settings['tools']['data']['cache']['interval']['value'];

                    if ( function_exists( 'is_multisite' ) && is_multisite() )
                        set_site_transient( $transient_name, $recents, $expiration );
                    else
                        set_transient( $transient_name, $recents, $expiration );

                    $recently_transients = get_site_option('recently_transients');

                    if ( !$recently_transients ) {
                        $recently_transients = array( $transient_name );
                        add_site_option('recently_transients', $recently_transients);
                    } else {
                        if ( !in_array($transient_name, $recently_transients) ) {
                            $recently_transients[] = $transient_name;
                            update_site_option('recently_transients', $recently_transients);
                        }
                    }
                }
            } else {
                $recents = $this->_query_posts( $instance );
            }

            // Allow WP themers / coders access to raw data
            // so they can build their own output
            if ( has_filter( 'recently_custom_html' ) ) {
                return apply_filters( 'recently_custom_html', $recents, $instance );
            }

            // Items found, render them
            if ( $recents->have_posts() ) {

                // HTML wrapper
                if ($instance['markup']['custom_html']) {
                    $content .= "\n" . htmlspecialchars_decode($instance['markup']['recently-start'], ENT_QUOTES) ."\n";
                } else {
                    $content .= "\n" . "<ul class=\"recently-list" . ( $instance['thumbnail']['active'] ? ' recently-with-thumbs' : '' ) . "\">" . "\n";
                }

                // Loop through items
                while ( $recents->have_posts() ) {
                    $recents->the_post();
                    $content .= $this->render_recent_item( $instance );
                }

                // END HTML wrapper
                if ($instance['markup']['custom_html']) {
                    $content .= "\n". htmlspecialchars_decode($instance['markup']['recently-end'], ENT_QUOTES) ."\n";
                } else {
                    $content .= "\n". "</ul>". "\n";
                }

            } // Nothing to display
            else {
                $content = apply_filters( 'recently_no_data', "<p class=\"recently-no-data\">".__('Sorry, nothing to show yet.', $this->plugin_slug)."</p>" );
            }

            // Restore original Post Data
            wp_reset_postdata();

            return $content;

        } // end get_recents

        /**
         * Returns the formatted post.
         *
         * @since	1.0.0
         * @global	object 		$post
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        private function render_recent_item($instance) {
            global $post;

            if ( defined('ICL_LANGUAGE_CODE') && function_exists('icl_object_id') ) {
                $current_id = icl_object_id( $post->ID, get_post_type( $post->ID ), true, ICL_LANGUAGE_CODE );
                $permalink = get_permalink( $current_id );
            } // Get original permalink
            else {
                $permalink = get_permalink($post->ID);
            }

            $title = $this->_get_title($post, $instance);
            $title_sub = $this->_get_title_sub($post, $instance);
            $thumb = $this->_get_thumb($post, $instance);
            $excerpt = $this->_get_excerpt($post, $instance);
            $rating = $this->_get_rating($post, $instance);

            // PUTTING IT ALL TOGETHER
            // build custom layout
            if ($instance['markup']['custom_html']) {

                $pageviews = $this->_get_pageviews($post, $instance);
                $comments = $this->_get_comments($post, $instance);
                $date = $this->_get_date($post, $instance);
                $author = $this->_get_author($post, $instance);
                $post_tax = $this->_get_post_tax($post, $instance);

                $data = array(
                    'title' => '<a href="'. $permalink .'" title="'. esc_attr($title) .'" class="recently-post-title" target="' . $this->user_settings['tools']['markup']['link']['attr']['target'] . '" rel="' . esc_attr($this->user_settings['tools']['markup']['link']['attr']['rel']) . '">'. $title_sub .'</a>',
                    'summary' => $excerpt,
                    'metadata' => join(' | ', $this->_get_meta($post, $instance)),
                    'img' => ( !empty($thumb) ) ? '<a href="'. $permalink .'" title="'. esc_attr($title) .'" target="' . $this->user_settings['tools']['markup']['link']['attr']['target'] . '" rel="' . esc_attr($this->user_settings['tools']['markup']['link']['attr']['rel']) . '">'. $thumb .'</a>' : '',
                    'img_no_link' => $thumb,
                    'id' => $post->ID,
                    'url' => $permalink,
                    'text_title' => esc_attr($title),
                    'taxonomy' => $post_tax,
                    'author' => '<a href="' . get_author_posts_url($post->post_author) . '">' . $author . '</a>',
                    'views' => number_format_i18n( $pageviews ),
                    'comments' => number_format_i18n( $comments ),
                    'date' => $date
                );

                $content = $this->format_content( htmlspecialchars_decode($instance['markup']['post-html'], ENT_QUOTES ), $data, $instance['rating'] ). "\n";

            }
            // build regular layout
            else {

                $thumb = ( !empty($thumb) )
                  ? '<a ' . ( ( $this->current_post_id == $post->ID ) ? '' : 'href="' . $permalink . '"' ) . ' title="' . esc_attr($title) . '" target="' . $this->user_settings['tools']['markup']['link']['attr']['target'] . '" rel="' . esc_attr($this->user_settings['tools']['markup']['link']['attr']['rel']) . '" class="recently-thumbnail-wrapper">' . $thumb . '</a> '
                  : '';

                $metadata = join(' | ', $this->_get_meta($post, $instance));
                $metadata = ( !empty($metadata) )
                  ? ' <span class="recently-meta">' . $metadata . '</span> '
                  : '';

                $content =
                    '<li>'
                    . $thumb
                    . '<a ' . ( ( $this->current_post_id == $post->ID ) ? '' : 'href="' . $permalink . '"' ) . ' title="' . esc_attr($title) . '" class="recently-post-title" target="' . $this->user_settings['tools']['markup']['link']['attr']['target'] . '" rel="' . esc_attr($this->user_settings['tools']['markup']['link']['attr']['rel']) . '">' . $title_sub . '</a> '
                    . $excerpt . $metadata
                    . $rating
                    . "</li>\n";
            }

            return $content;
        } // end render_recent_item

        /**
         * Cache.
         *
         * @since	1.0.0
         * @param	string $func function name
         * @param	mixed $default
         * @return	mixed
         */
        private function &__cache($func, $default = null) {

            static $cache;

            if ( !isset($cache) ) {
                $cache = array();
            }

            if ( !isset($cache[$func]) ) {
                $cache[$func] = $default;
            }

            return $cache[$func];

        } // end __cache

        /**
         * Gets post title.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_title($p, $instance) {

            $cache = &$this->__cache(__FUNCTION__, array());

            if ( isset($cache[$p->ID]) ) {
                return $cache[$p->ID];
            }

            // WPML support
            if ( defined('ICL_LANGUAGE_CODE') && function_exists('icl_object_id') ) {
                $current_id = icl_object_id( $p->ID, get_post_type( $p->ID ), true, ICL_LANGUAGE_CODE );
                $title = get_the_title( $current_id );
            } // Use ol' plain title
            else {
                $title = $p->post_title;
            }

            // Strip HTML tags
            $title = strip_tags($title);

            return $cache[$p->ID] = apply_filters('the_title', $title, $p->ID);

        } // end _get_title

        /**
         * Gets substring of post title.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_title_sub($p, $instance) {

            $cache = &$this->__cache(__FUNCTION__, array());

            if ( isset($cache[$p->ID]) ) {
                return $cache[$p->ID];
            }

            // TITLE
            $title_sub = $this->_get_title($p, $instance);

            // truncate title
            if ($instance['shorten_title']['active']) {
                // by words
                if (isset($instance['shorten_title']['words']) && $instance['shorten_title']['words']) {

                    $words = explode(" ", $title_sub, $instance['shorten_title']['length'] + 1);
                    if (count($words) > $instance['shorten_title']['length']) {
                        array_pop($words);
                        $title_sub = rtrim( implode(" ", $words), ",." ) . " ...";
                    }

                }
                elseif (strlen($title_sub) > $instance['shorten_title']['length']) {
                    $title_sub = rtrim( mb_substr($title_sub, 0, $instance['shorten_title']['length'], $this->charset), " ,." ) . "...";
                }
            }

            return $cache[$p->ID] = $title_sub;

        } // end _get_title_sub

        /**
         * Gets post's excerpt.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_excerpt($p, $instance) {

            $excerpt = '';

            // EXCERPT
            if ($instance['post-excerpt']['active']) {

                $excerpt = trim($this->_get_summary($p->ID, $instance));

                if (!empty($excerpt) && !$instance['markup']['custom_html']) {
                    $excerpt = '<span class="recently-excerpt">' . $excerpt . '</span>';
                }

            }

            return $excerpt;

        } // end _get_excerpt

        /**
         * Gets post's thumbnail.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_thumb($p, $instance) {

            if ( !$instance['thumbnail']['active'] || !$this->thumbs ) {
                return '';
            }

            $tbWidth = $instance['thumbnail']['width'];
            $tbHeight = $instance['thumbnail']['height'];
            $crop = $instance['thumbnail']['crop'];
            $title = $this->_get_title($p, $instance);

            $thumb = '';

            // get image from custom field
            if ($this->user_settings['tools']['markup']['thumbnail']['source'] == "custom_field") {
                $path = get_post_meta($p->ID, $this->user_settings['tools']['thumbnail']['field'], true);

                if ($path != '') {
                    // user has requested to resize cf image
                    if ( $this->user_settings['tools']['markup']['thumbnail']['resize'] ) {
                        $thumb .= $this->get_img($p, null, $path, array($tbWidth, $tbHeight), $crop, $this->user_settings['tools']['markup']['thumbnail']['source'], $title);
                    }
                    // use original size
                    else {
                        $thumb .= $this->_render_image($path, array($tbWidth, $tbHeight), 'recently-thumbnail recently-cf', $title);
                    }
                }
                else {
                    $thumb .= $this->_render_image($this->default_thumbnail, array($tbWidth, $tbHeight), 'recently-thumbnail recently-cf-def', $title);
                }
            }
            // get image from post / Featured Image
            else {

                // User wants to use the Featured Image using the 'stock' sizes, get stock thumbnails
                if ( 'predefined' == $instance['thumbnail']['build'] && 'featured' == $this->user_settings['tools']['markup']['thumbnail']['source'] ) {

                    // The has_post_thumbnail() functions requires theme's 'post-thumbnails' support, otherwise an error will be thrown
                    if ( current_theme_supports( 'post-thumbnails' ) ) {

                        // Featured image found, retrieve it
                        if ( has_post_thumbnail($p->ID) ) {
                            $size = null;

                            foreach ( $this->default_thumbnail_sizes as $name => $attr ) :
                                if ( $attr['width'] == $tbWidth && $attr['height'] == $tbHeight && $attr['crop'] == $crop ) {
                                    $size = $name;
                                    break;
                                }
                            endforeach;

                            // Couldn't find a matching size (this should like never happen, but...) let's use width & height instead
                            if ( null == $size ) {
                                $size = array( $tbWidth, $tbHeight );
                            }

                            $thumb .= get_the_post_thumbnail( $p->ID, $size, array( 'class' => 'recently-thumbnail recently-featured-stock' ) );
                        }
                        // No featured image found
                        else {
                            $thumb .= $this->_render_image($this->default_thumbnail, array($tbWidth, $tbHeight), 'recently-thumbnail recently-featured-def', $title);
                        }

                    } // Current theme does not support 'post-thumbnails' feature
                    else {
                        $thumb .= $this->_render_image($this->default_thumbnail, array($tbWidth, $tbHeight), 'recently-thumbnail recently-featured-def', $title, 'No post-thumbnail support');
                    }

                }
                // Get/generate custom thumbnail
                else {
                    $thumb .= $this->get_img($p, $p->ID, null, array($tbWidth, $tbHeight), $crop, $this->user_settings['tools']['markup']['thumbnail']['source'], $title);
                }

            }

            return $thumb;

        } // end _get_thumb

        /**
         * Gets post's views.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	int|float
         */
        protected function _get_pageviews($p, $instance) {

            $pageviews = 0;

            if ( $instance['meta_tag']['views'] && function_exists('wpp_get_views') ) {

                // WordPress Popular Posts support
                if ( function_exists('wpp_get_views') ) {
                    $pageviews = wpp_get_views( $p->ID, NULL, false );
                } // WP-PostViews support
                elseif ( $this->is_plugin_active('wp-postviews/wp-postviews.php') ) {
                    // this plugin stores views data in the post meta table
                    if ( !$pageviews = get_post_meta( $p->ID, 'views', true ) ) {
                        $pageviews = 0;
                    }
                } // Top-10 support
                elseif ( function_exists('get_tptn_post_count_only') ) {
                    $blog_id = false;

                    if ( is_multisite() ) {
                        $blog_id = get_current_blog_id();
                    }

                    $pageviews = get_tptn_post_count_only( $p->ID, 'total', $blog_id );
                }

            }

            return apply_filters( 'recently_get_views', $pageviews, $p->ID );

        } // end _get_pageviews

        /**
         * Gets post's comment count.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	int
         */
        protected function _get_comments($p, $instance) {

            $comments = ($instance['meta_tag']['comment_count'])
              ? get_comments_number()
              : 0;

            return $comments;

        } // end _get_comments

        /**
         * Gets post's rating.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_rating($p, $instance) {

            $cache = &$this->__cache(__FUNCTION__, array());

            if ( isset($cache[$p->ID]) ) {
                return $cache[$p->ID];
            }

            $rating = '';

            // RATING
            if (function_exists('the_ratings') && $instance['rating']) {
                $rating = '<span class="recently-rating">' . the_ratings('span', $p->ID, false) . '</span>';
            }

            return $cache[$p->ID] = $rating;
        } // end _get_rating

        /**
         * Gets post's author.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_author($p, $instance) {

            $cache = &$this->__cache(__FUNCTION__, array());

            if ( isset($cache[$p->ID]) ) {
                return $cache[$p->ID];
            }

            $author = ($instance['meta_tag']['author'])
              ? get_the_author()
              : "";

            return $cache[$p->ID] = $author;

        } // end _get_author

        /**
         * Gets post's date.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_date($p, $instance) {

            $cache = &$this->__cache(__FUNCTION__, array());

            if ( isset($cache[$p->ID]) ) {
                return $cache[$p->ID];
            }

            $date = get_the_time( $instance['meta_tag']['date']['format'] );
            return $cache[$p->ID] = $date;

        } // end _get_date

        /**
         * Gets post's taxonomies.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	string
         */
        protected function _get_post_tax($p, $instance) {

            $post_tax = null;

            if ($instance['meta_tag']['taxonomy']['active'] && !empty($instance['meta_tag']['taxonomy']['names'])) {

                $cache = &$this->__cache(__FUNCTION__, array());

                if ( isset($cache[$p->ID]) ) {
                    return $cache[$p->ID];
                }

                // Get terms
                foreach( $instance['meta_tag']['taxonomy']['names'] as $tax_name ) {
                    $terms = get_the_terms( $p->ID, $tax_name );

                    if ( !empty($terms) && !is_wp_error($terms) ) {
                        foreach ( $terms as $term ) {
                            $post_tax[$tax_name][] = '<a href="' . get_term_link( $term->slug, $tax_name ) . '">' . $term->name . '</a>';
                        }
                    }
                }

                return $cache[$p->ID] = $post_tax;

            }

            return $post_tax;

        } // end _get_post_tax

        /**
         * Gets meta data.
         *
         * @since	1.0.0
         * @param	object	p
         * @param	array	instance	The current instance of the widget / shortcode parameters
         * @return	array
         */
        protected function _get_meta($p, $instance) {

            $cache = &$this->__cache(__FUNCTION__ . md5(json_encode($instance)), array());

            if ( isset($cache[$p->ID]) ) {
                return $cache[$p->ID];
            }

            $meta = array();

            // STATS
            // comments
            if ($instance['meta_tag']['comment_count']) {
                $comments = $this->_get_comments($p, $instance);
                /* translators: %s placeholder will be replaced with the comments count value */
                $comments_text = sprintf( _n('1 comment', '%s comments', $comments, $this->plugin_slug), number_format_i18n( $comments ) );
                $meta[] = '<span class="recently-comments">' . $comments_text . '</span>';
            }

            // views
            if ($instance['meta_tag']['views']) {
                $pageviews = $this->_get_pageviews($p, $instance);
                /* translators: %s placeholder will be replaced with the views count vaLue */
                $views_text = sprintf( _n('1 view', '%s views', $pageviews, $this->plugin_slug), number_format_i18n( $pageviews ) );
                $meta[] = '<span class="recently-views">' . $views_text . "</span>";
            }

            // author
            if ($instance['meta_tag']['author']) {
                $author = $this->_get_author($p, $instance);
                $display_name = '<a href="' . get_author_posts_url($p->post_author) . '">' . $author . '</a>';
                /* translators: %s placeholder will be replaced with the author name */
                $meta[] = '<span class="recently-author">' . sprintf(__('by %s', $this->plugin_slug), $display_name).'</span>';
            }

            // date
            if ($instance['meta_tag']['date']['active']) {
                $date = $this->_get_date($p, $instance);
                /* translators: %s placeholder will be replaced with the post date */
                $meta[] = '<span class="recently-date">' . sprintf(__('posted on %s', $this->plugin_slug), $date) . '</span>';
            }

            // taxonomies
            if ($instance['meta_tag']['taxonomy']['active']) {
                $post_tax = $this->_get_post_tax($p, $instance);

                if ($post_tax) {

                    $taxes = '';

                    foreach( $post_tax as $tax_name => $term_links ) {
                        if ( $tax_data = get_taxonomy($tax_name) ) {
                            $taxes .= '<span class="taxonomy-name taxonomy-' . $tax_name . '">' . $tax_data->label . ':</span> ' . implode(', ', $term_links) . ' - ';
                        }
                    }

                    $taxes = rtrim( $taxes, ' - ');
                    $meta[] = '<span class="recently-taxonomies">' . $taxes . '</span>';

                }
            }

            return $cache[$p->ID] = $meta;

        } // end _get_meta

        /**
         * Retrieves / creates the post thumbnail.
         *
         * @since	1.0.0
         * @param	int	id			Post ID
         * @param	string	url		Image URL
         * @param	array	dim		Thumbnail width & height
         * @param	string	source	Image source
         * @return	string
         */
        private function get_img($p, $id = null, $url = null, $dim = array(80, 80), $crop = true, $source = "featured", $title) {

            if ( (!$id || empty($id) || !$this->is_numeric($id)) && (!$url || empty($url)) ) {
                return $this->_render_image($this->default_thumbnail, $dim, 'recently-thumbnail recently-def-noID', $title);
            }

            // Get image by post ID (parent)
            if ( $id ) {
                $file_path = $this->get_image_file_paths($id, $source);

                // No images found, return default thumbnail
                if ( !$file_path ) {
                    return $this->_render_image($this->default_thumbnail, $dim, 'recently-thumbnail recently-def-noPath recently-' . $source, $title);
                }
            }
            // Get image from URL
            else {
                // sanitize URL, just in case
                $image_url = esc_url( $url );
                // remove querystring
                preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $image_url, $matches);
                $image_url = $matches[0];

                $attachment_id = $this->get_attachment_id($image_url);

                // Image is hosted locally
                if ( $attachment_id ) {
                    $file_path = get_attached_file($attachment_id);
                }
                // Image is hosted outside WordPress
                else {
                    $external_image = $this->fetch_external_image($p->ID, $image_url);

                    if ( !$external_image ) {
                        return $this->_render_image($this->default_thumbnail, $dim, 'recently-thumbnail recently-def-noPath recently-no-external', $title);
                    }

                    $file_path = $external_image;
                }
            }

            $file_info = pathinfo($file_path);

            // there is a thumbnail already
            if ( file_exists(trailingslashit($this->uploads_dir['basedir']) . $p->ID . '-' . $source . '-' . $dim[0] . 'x' . $dim[1] . '.' . $file_info['extension']) ) {
                return $this->_render_image( trailingslashit($this->uploads_dir['baseurl']) . $p->ID . '-' . $source . '-' . $dim[0] . 'x' . $dim[1] . '.' . $file_info['extension'], $dim, 'recently-thumbnail recently-cached-thumb recently-' . $source, $title );
            }

            return $this->image_resize($p, $file_path, $dim, $crop, $source);

        } // end get_img

        /**
         * Resizes image.
         *
         * @since	1.0.0
         * @param	object	p			Post object
         * @param	string	path		Image path
         * @param	array	dimension	Image's width and height
         * @param	string	source		Image source
         * @return	string
         */
        private function image_resize($p, $path, $dimension, $crop, $source) {

            $image = wp_get_image_editor($path);

            // valid image, create thumbnail
            if ( !is_wp_error($image) ) {
                $file_info = pathinfo($path);

                $image->resize($dimension[0], $dimension[1], $crop);
                $new_img = $image->save( trailingslashit($this->uploads_dir['basedir']) . $p->ID . '-' . $source . '-' . $dimension[0] . 'x' . $dimension[1] . '.' . $file_info['extension'] );

                if ( is_wp_error($new_img) ) {
                    return $this->_render_image($this->default_thumbnail, $dimension, 'recently-thumbnail recently-imgeditor-error recently-' . $source, '', $new_img->get_error_message());
                }

                return $this->_render_image( trailingslashit($this->uploads_dir['baseurl']) . $new_img['file'], $dimension, 'recently-thumbnail recently-imgeditor-thumb recently-' . $source, '');
            }

            // ELSE
            // image file path is invalid
            return $this->_render_image($this->default_thumbnail, $dimension, 'recently-thumbnail recently-imgeditor-error recently-' . $source, '', $image->get_error_message());

        } // end image_resize

        /**
         * Get image absolute path / URL.
         *
         * @since	1.0.0
         * @param	int	id			Post ID
         * @param	string	source	Image source
         * @return	array
         */
        private function get_image_file_paths($id, $source) {

            $file_path = '';

            // get thumbnail path from the Featured Image
            if ($source == "featured") {

                // thumb attachment ID
                $thumbnail_id = get_post_thumbnail_id($id);

                if ($thumbnail_id) {
                    // image path
                    return get_attached_file($thumbnail_id);
                }

            }
            // get thumbnail path from post content
            elseif ($source == "first_image") {

                /** @var wpdb $wpdb */
                global $wpdb;

                if ( $content = $wpdb->get_var( "SELECT post_content FROM {$wpdb->posts} WHERE ID = {$id};" ) ) {

                    // at least one image has been found
                    if ( preg_match( '/<img[^>]+>/i', $content, $img ) ) {

                        // get img src attribute from the first image found
                        preg_match( '/(src)="([^"]*)"/i', $img[0], $src_attr );

                        if ( isset($src_attr[2]) && !empty($src_attr[2]) ) {

                            // image from Media Library
                            if ( $attachment_id = $this->get_attachment_id( $src_attr[2] ) ) {

                                $file_path = get_attached_file($attachment_id);

                                // There's a file path, so return it
                                if ( !empty($file_path) ) {
                                    return $file_path;
                                }

                            } // external image?
                            else {
                                return $this->fetch_external_image($id, $src_attr[2]);
                            }

                        }

                    }

                }

            }

            return false;

        } // end get_image_file_paths

        /**
         * Render image tag.
         *
         * @since	1.0.0
         * @param	string	src			Image URL
         * @param	array	dimension	Image's width and height
         * @param	string	class		CSS class
         * @param	string	title		Image's title/alt attribute
         * @param	string	error		Error, if the image could not be created
         * @return	string
         */
        protected function _render_image($src, $dimension, $class, $title = "", $error = null) {

            $msg = '';

            if ($error) {
                $msg = '<!-- ' . $error . ' --> ';
            }

            return apply_filters( 'recently_render_image', $msg .
            '<img src="' . $src . '" width=' . $dimension[0] . ' height=' . $dimension[1] . ' title="' . esc_attr($title) . '" alt="' . esc_attr($title) . '" class="' . $class . '" />' );

        } // _render_image

        /**
        * Get the Attachment ID for a given image URL.
        *
        * @since	1.0.0
        * @author	Frankie Jarrett
        * @link		http://frankiejarrett.com/get-an-attachment-id-by-url-in-wordpress/
        * @param	string	url
        * @return	bool|int
        */
        private function get_attachment_id($url) {

            // Split the $url into two parts with the wp-content directory as the separator.
            $parse_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

            // Get the host of the current site and the host of the $url, ignoring www.
            $this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
            $file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

            // Return nothing if there aren't any $url parts or if the current host and $url host do not match.
            if ( ! isset( $parse_url[1] ) || empty( $parse_url[1] ) || ( $this_host != $file_host ) ) {
                return false;
            }

            // Now we're going to quickly search the DB for any attachment GUID with a partial path match.
            // Example: /uploads/2013/05/test-image.jpg
            global $wpdb;

            if ( !$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parse_url[1] ) ) ) {
                // Maybe it's a resized image, so try to get the full one
                $parse_url[1] = preg_replace( '/-[0-9]{1,4}x[0-9]{1,4}\.(jpg|jpeg|png|gif|bmp)$/i', '.$1', $parse_url[1] );
                $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parse_url[1] ) );
            }

            // Returns null if no attachment is found.
            return isset($attachment[0]) ? $attachment[0] : NULL;

        } // get_attachment_id

        /**
        * Fetchs external images.
        *
        * @since	1.0.0
        * @param	string	url
        * @return	bool|int
        */
        private function fetch_external_image($id, $url){

            $full_image_path = trailingslashit( $this->uploads_dir['basedir'] ) . "{$id}_". sanitize_file_name( rawurldecode(wp_basename( $url )) );

            // if the file exists already, return URL and path
            if ( file_exists($full_image_path) )
                return $full_image_path;

            $accepted_status_codes = array( 200, 301, 302 );
            $response = wp_remote_head( $url, array( 'timeout' => 5, 'sslverify' => false ) );

            if ( !is_wp_error($response) && in_array(wp_remote_retrieve_response_code($response), $accepted_status_codes) ) {

                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                $url = str_replace( 'https://', 'http://', $url );
                $tmp = download_url( $url );

                if ( !is_wp_error( $tmp ) ) {

                    if ( function_exists('exif_imagetype') ) {
                        $image_type = exif_imagetype( $tmp );
                    } else {
                        $image_type = getimagesize( $tmp );
                        $image_type = ( isset($image_type[2]) ) ? $image_type[2] : NULL;
                    }

                    if ( in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)) ) {

                        // move file to Uploads
                        if ( @rename($tmp, $full_image_path) ) {
                            // borrowed from WP - set correct file permissions
                            $stat = stat( dirname( $full_image_path ));
                            $perms = $stat['mode'] & 0000644;
                            @chmod( $full_image_path, $perms );

                            return $full_image_path;
                        }

                    }

                    // remove temp file
                    @unlink( $tmp );

                }

            }

            return false;

        } // end fetch_external_image

        /**
         * Builds post's excerpt
         *
         * @since	1.0.0
         * @global	object	wpdb
         * @param	int	post ID
         * @param	array	widget instance
         * @return	string
         */
        protected function _get_summary($id, $instance){

            if ( !$this->is_numeric($id) )
                return false;

            global $wpdb;

            $excerpt = "";

            // WPML support, get excerpt for current language
            if ( defined('ICL_LANGUAGE_CODE') && function_exists('icl_object_id') ) {
                $id = icl_object_id( $id, get_post_type( $id ), true, ICL_LANGUAGE_CODE );
            }

            $the_post = get_post( $id );
            $excerpt = ( empty($the_post->post_excerpt) )
              ? $the_post->post_content
              : $the_post->post_excerpt;

            // remove caption tags
            $excerpt = preg_replace( "/\[caption.*\[\/caption\]/", "", $excerpt );

            // remove Flash objects
            $excerpt = preg_replace( "/<object[0-9 a-z_?*=\":\-\/\.#\,\\n\\r\\t]+/smi", "", $excerpt );

            // remove Iframes
            $excerpt = preg_replace( "/<iframe.*?\/iframe>/i", "", $excerpt);

            // remove WP shortcodes
            $excerpt = strip_shortcodes( $excerpt );

            // remove HTML tags if requested
            if ( $instance['post-excerpt']['keep_format'] ) {
                $excerpt = strip_tags($excerpt, '<a><b><i><em><strong>');
            } else {
                $excerpt = strip_tags($excerpt);
                // remove URLs, too
                $excerpt = preg_replace( '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS', '', $excerpt );
            }

            // Fix RSS CDATA tags
            $excerpt = str_replace( ']]>', ']]&gt;', $excerpt );

            // do we still have something to display?
            if ( !empty($excerpt) ) {

                // truncate excerpt
                if ( isset($instance['post-excerpt']['words']) && $instance['post-excerpt']['words'] ) { // by words

                    $words = explode(" ", $excerpt, $instance['post-excerpt']['length'] + 1);

                    if ( count($words) > $instance['post-excerpt']['length'] ) {
                        array_pop($words);
                        $excerpt = rtrim( implode(" ", $words), ".," ) . " ...";
                    }

                } else { // by characters

                    if ( strlen($excerpt) > $instance['post-excerpt']['length'] ) {
                        $excerpt = rtrim( mb_substr( $excerpt, 0, $instance['post-excerpt']['length'], $this->charset ), ".," ) . "...";
                    }

                }

            }

            // Balance tags, if needed
            if ( $instance['post-excerpt']['keep_format'] ) {
                $excerpt = force_balance_tags($excerpt);
            }

            return $excerpt;

        } // _get_summary

        /**
         * Parses content tags
         *
         * @since	1.0.0
         * @param	string	HTML string with content tags
         * @param	array	Post data
         * @param	bool	Used to display post rating (if functionality is available)
         * @return	string
         */
        private function format_content($string, $data = array(), $rating) {

            if (empty($string) || (empty($data) || !is_array($data)))
                return false;

            $params = array();
            $pattern = '/\{(excerpt|summary|metadata|title|image|thumb|thumb_img|rating|score|url|text_title|author|taxonomy|views|comments|date)\}/i';
            preg_match_all($pattern, $string, $matches);

            array_map('strtolower', $matches[0]);

            if ( in_array("{pid}", $matches[0]) ) {
                $string = str_replace( "{pid}", $data['id'], $string );
            }

            if ( in_array("{title}", $matches[0]) ) {
                $string = str_replace( "{title}", $data['title'], $string );
            }

            if ( in_array("{metadata}", $matches[0]) ) {
                $string = str_replace( "{metadata}", $data['metadata'], $string );
            }

            if ( in_array("{excerpt}", $matches[0]) || in_array("{summary}", $matches[0]) ) {
                $string = str_replace( array("{excerpt}", "{summary}"), $data['summary'], $string );
            }

            if ( in_array("{image}", $matches[0]) || in_array("{thumb}", $matches[0]) ) {
                $string = str_replace( array("{image}", "{thumb}"), $data['img'], $string );
            }

            if ( in_array("{thumb_img}", $matches[0]) ) {
                $string = str_replace( "{thumb_img}", $data['img_no_link'], $string );
            }

            // WP-PostRatings check
            if ( $rating ) {
                if ( function_exists('the_ratings_results') && in_array("{rating}", $matches[0]) ) {
                    $string = str_replace( "{rating}", the_ratings_results($data['id']), $string );
                }

                if ( function_exists('expand_ratings_template') && in_array("{score}", $matches[0]) ) {
                    $string = str_replace( "{score}", expand_ratings_template('%RATINGS_SCORE%', $data['id']), $string);
                    // removing the redundant plus sign
                    $string = str_replace('+', '', $string);
                }
            }

            if ( in_array("{url}", $matches[0]) ) {
                $string = str_replace( "{url}", $data['url'], $string );
            }

            if ( in_array("{text_title}", $matches[0]) ) {
                $string = str_replace( "{text_title}", $data['text_title'], $string );
            }

            if ( in_array("{author}", $matches[0]) ) {
                $string = str_replace( "{author}", $data['author'], $string );
            }

            if ( in_array("{taxonomy}", $matches[0]) ) {
                $taxonomies = '';

                if ( is_array($data['taxonomy']) && !empty($data['taxonomy']) ) {

                    foreach( $data['taxonomy'] as $tax => $links ) {
                        $taxonomies .= '<span class="recently-taxonomy taxonomy-' . $tax . '">';

                        for ($l = 0; $l < count($links); $l++ )
                            $taxonomies .= $links[$l] . ', ';

                        $taxonomies = rtrim( $taxonomies, ', ' );

                        $taxonomies .= '</span> ';
                    }

                }

                $taxonomies = trim( $taxonomies );

                $string = str_replace( "{taxonomy}", $taxonomies, $string );
            }

            if ( in_array("{views}", $matches[0]) ) {
                $string = str_replace( "{views}", $data['views'], $string );
            }

            if ( in_array("{comments}", $matches[0]) ) {
                $string = str_replace( "{comments}", $data['comments'], $string );
            }

            if ( in_array("{date}", $matches[0]) ) {
                $string = str_replace( "{date}", $data['date'], $string );
            }

            return $string;

        } // end format_content

        /**
         * Returns HTML list via AJAX
         *
         * @since	1.0.0
         * @return	string
         */
        public function get_recently( ) {

            if ( $this->is_numeric($_GET['widget_id']) && ($_GET['widget_id'] != '') ) {
                $id = $_GET['widget_id'];
            } else {
                die("Invalid ID");
            }

            $widget_instances = $this->get_settings();

            if ( isset($widget_instances[$id]) ) {
                echo $this->get_recents( $widget_instances[$id] );
            } else {
                echo "Invalid Widget ID";
            }

            exit();

        } // end get_recently

        /*--------------------------------------------------*/
        /* Helper functions
        /*--------------------------------------------------*/

        /**
         * Checks if a given plugin is active
         *
         * @since	1.0.0
         * @return	bool
         */
        private function is_plugin_active( $plugin ) {

            if ( is_multisite() ) {
                $plugins = get_site_option( 'active_sitewide_plugins' );
                return isset( $plugins[$plugin] );
            }

            return in_array( $plugin, get_option( 'active_plugins' ) );

        }

        /**
         * Gets list of available thumbnails sizes
         *
         * @since	1.0.0
         * @link	http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
         * @param	string	$size
         * @return	array|bool
         */
        private function get_image_sizes( $size = '' ) {

            global $_wp_additional_image_sizes;

            $sizes = array();
            $get_intermediate_image_sizes = get_intermediate_image_sizes();

            // Create the full array with sizes and crop info
            foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                    $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                    $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                    $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                    $sizes[ $_size ] = array(
                        'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                        'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                        'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
                    );

                }

            }

            // Get only 1 size if found
            if ( $size ) {

                if( isset( $sizes[ $size ] ) ) {
                    return $sizes[ $size ];
                } else {
                    return false;
                }

            }

            return $sizes;
        }

        /**
         * Gets post/page ID if current page is singular
         *
         * @since	1.0.0
         */
        public function is_single() {
            if ( (is_single() || is_page()) && !is_front_page() && !is_preview() && !is_trackback() && !is_feed() && !is_robots() ) {
                global $post;
                $this->current_post_id = ( is_object($post) ) ? $post->ID : 0;
            } else {
                $this->current_post_id = 0;
            }
        } // end is_single

        /**
         * Checks for valid number
         *
         * @since	1.0.0
         * @param	int	number
         * @return	bool
         */
        private function is_numeric($number){
            return !empty($number) && is_numeric($number) && (intval($number) == floatval($number));
        }

        /**
         * Merges two associative arrays recursively
         *
         * @since	1.0.0
         * @link	http://www.php.net/manual/en/function.array-merge-recursive.php#92195
         * @param	array	array1
         * @param	array	array2
         * @return	array
         */
        private function merge_array_r( array &$array1, array &$array2 ) {

            $merged = $array1;

            foreach ( $array2 as $key => &$value ) {

                if ( is_array( $value ) && isset ( $merged[$key] ) && is_array( $merged[$key] ) ) {
                    $merged[$key] = $this->merge_array_r( $merged[$key], $value );
                } else {
                    $merged[$key] = $value;
                }
            }

            return $merged;

        } // end merge_array_r

        /**
         * Debug function.
         *
         * @since	1.0.0
         * @param	mixed $v variable to display with var_dump()
         * @param	mixed $v,... unlimited optional number of variables to display with var_dump()
         */
        private function debug($v) {

            if ( !defined('WP_DEBUG') || !WP_DEBUG )
                return;

            foreach (func_get_args() as $arg) {

                print "<pre>";
                var_dump($arg);
                print "</pre>";

            }

        } // end debug

    } // end class

}
