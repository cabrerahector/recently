<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @link       https://cabrerahector.com/
 * @since      2.0.0
 *
 * @package    Recently
 * @subpackage Recently/admin
 */

/**
 * The admin-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Recently
 * @subpackage Recently/admin
 * @author     Hector Cabrera <me@cabrerahector.com>
 */
class Recently_Admin {

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
    private $options = array();

    /**
     * Slug of the plugin screen.
     *
     * @since	2.0.0
     * @var		string
     */
    protected $plugin_screen_hook_suffix = NULL;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = Recently_Settings::get( 'admin_options' );

    }

    /**
     * Fired when a new blog is activated on WP Multisite.
     *
     * @since    2.0.0
     * @param    int      blog_id    New blog ID
     */
    public function activate_new_site( $blog_id ){

        if ( 1 !== did_action( 'wpmu_new_blog' ) )
            return;

        // run activation for the new blog
        switch_to_blog( $blog_id );
        Recently_Activator::track_new_site();

        // switch back to current blog
        restore_current_blog();

    } // end activate_new_site

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_styles() {

        if ( !isset( $this->plugin_screen_hook_suffix ) ) {
            return;
        }

        $screen = get_current_screen();

        if ( isset( $screen->id ) && $screen->id == $this->plugin_screen_hook_suffix ) {
            wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/vendor/font-awesome.min.css', array(), '4.7.0', 'all' );
            wp_enqueue_style( 'recently-admin-styles', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
        }

    }

    /**
     * Register the stylesheets for the admin-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script( 'recently-admin-widget-script', plugin_dir_url( __FILE__ ) . 'js/widget-admin.js', array('jquery'), $this->version, true );

        if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
            return;
        }

        $screen = get_current_screen();

        if ( $screen->id == $this->plugin_screen_hook_suffix ) {

            wp_enqueue_script( 'thickbox' );
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'media-upload' );
            wp_register_script( 'recently-admin-script', plugin_dir_url( __FILE__ ) . 'js/admin.js', array('jquery'), $this->version, true );
            wp_localize_script( 'recently-admin-script', 'recently_admin_params', array(
                'nonce' => wp_create_nonce( "recently_admin_nonce" )
            ));
            wp_enqueue_script( 'recently-admin-script' );

        }

    }

    /**
     * Hooks into getttext to change upload button text when uploader is called by Recently.
     *
     * @since	2.0.0
     */
    public function thickbox_setup() {

        global $pagenow;

        if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
            add_filter( 'gettext', array( $this, 'replace_thickbox_text' ), 1, 3 );
        }

    } // end thickbox_setup

    /**
     * Replaces upload button text when uploader is called by Recently.
     *
     * @since	2.0.0
     * @param	string	translated_text
     * @param	string	text
     * @param	string	domain
     * @return	string
     */
    public function replace_thickbox_text( $translated_text, $text, $domain ) {

        if ( 'Insert into Post' == $text ) {
            $referer = strpos( wp_get_referer(), 'recently_admin' );
            if ( $referer != '' ) {
                return __( 'Upload', 'recently' );
            }
        }

        return $translated_text;

    } // end replace_thickbox_text

    /**
     * Adds help tab at top right of the screen.
     *
     * @since	2.0.0
     */
    public function add_contextual_help(){

        //get the current screen object
        $screen = get_current_screen();

        if ( isset( $screen->id ) && $screen->id == $this->plugin_screen_hook_suffix ){
            $screen->add_help_tab(
                array(
                    'id'        => 'recently_help_overview',
                    'title'     => __('Overview', 'recently'),
                    'content'   => "<p>" . __( "Welcome to Recently's Dashboard! In this screen you will tools to further tweak Recently to your needs and more!", "recently" ) . "</p>"
                )
            );
            $screen->add_help_tab(
                array(
                    'id'        => 'recently_help_donate',
                    'title'     => __('Like this plugin?', 'recently'),
                    'content'   => '
                        <p style="text-align: center;">' . __( 'Each donation motivates me to keep releasing free stuff for the WordPress community!', 'recently' ) . '</p>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="margin: 0; padding: 0; text-align: center;">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="PASXEM2E7JUVC">
                            <input type="image" src="//www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style="display: inline; margin: 0;">
                            <img alt="" border="0" src="//www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form>
                        <p style="text-align: center;">' . sprintf( __('You can <a href="%s" target="_blank">leave a review</a>, too!', 'recently'), 'https://wordpress.org/support/view/plugin-reviews/recently?rate=5#postform' ) . '</p>'
                )
            );

            // Help sidebar
            $screen->set_help_sidebar(
                sprintf(
                    __( '<p><strong>For more information:</strong></p><ul><li><a href="%1$s">Documentation</a></li><li><a href="%2$s">Support</a></li></ul>', 'recently' ),
                    "https://github.com/cabrerahector/recently/",
                    "https://wordpress.org/support/plugin/recently/"
                )
            );
        }

    }

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
            'recently',
            array( $this, 'display_plugin_admin_page' )
        );

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        include_once( plugin_dir_path(__FILE__) . 'partials/admin.php' );
    }

    /**
     * Registers Settings link on plugin description.
     *
     * @since	1.0.0
     * @param	array	$links
     * @param	string	$file
     * @return	array
     */
    public function add_plugin_settings_link( $links, $file ){

        $plugin_file = 'recently/recently.php';

        if (
            is_plugin_active( $plugin_file )
            && $plugin_file == $file
        ) {
            $links[] = '<a href="' . admin_url( 'options-general.php?page=recently' ) . '">' . __( 'Settings' ) . '</a>';
        }

        return $links;

    }

    /**
     * Register the Recently widget.
     *
     * @since    2.0.0
     */
    public function register_widget() {
        register_widget( 'Recently_Widget' );
    }

    /**
     * Flushes post's cached thumbnail(s) when the image is changed.
     *
     * @since    2.0.0
     *
     * @param    integer    $meta_id       ID of the meta data field
     * @param    integer    $object_id     Object ID
     * @param    string     $meta_key      Name of meta field
     * @param    string     $meta_value    Value of meta field
     */
    public function flush_post_thumbnail( $meta_id, $object_id, $meta_key, $meta_value ) {

        // User changed the featured image
        if ( '_thumbnail_id' == $meta_key ) {

            $recently_image = Recently_Image::get_instance();

            if ( $recently_image->can_create_thumbnails() ) {

                $recently_uploads_dir = $recently_image->get_plugin_uploads_dir();

                if ( is_array($recently_uploads_dir) && !empty($recently_uploads_dir) ) {

                    $files = glob( "{$recently_uploads_dir['basedir']}/{$object_id}-featured-*.*" ); // get all related images

                    if ( is_array($files) && !empty($files) ) {

                        foreach( $files as $file ){ // iterate files
                            if ( is_file( $file ) ) {
                                @unlink( $file ); // delete file
                            }
                        }

                    }

                }

            }

        }

    }

    /**
     * Truncates thumbnails cache on demand.
     *
     * @since	1.0.0
     */
    public function clear_thumbnails() {

        $recently_image = Recently_Image::get_instance();

        if ( $recently_image->can_create_thumbnails() ) {

            $recently_uploads_dir = $recently_image->get_plugin_uploads_dir();

            if ( is_array($recently_uploads_dir) && !empty($recently_uploads_dir) ) {

                $token = isset( $_POST['token'] ) ? $_POST['token'] : null;
                $key = get_option( "recently_rand" );

                if (
                    current_user_can( 'manage_options' )
                    && ( $token === $key )
                ) {

                    if ( is_dir( $recently_uploads_dir['basedir'] ) ) {

                        $files = glob( "{$recently_uploads_dir['basedir']}/*" ); // get all related images

                        if ( is_array($files) && !empty($files) ) {

                            foreach( $files as $file ){ // iterate files
                                if ( is_file( $file ) ) {
                                    @unlink( $file ); // delete file
                                }
                            }

                            echo 1;

                        } else {
                            echo 2;
                        }

                    } else {
                        echo 3;
                    }

                } else {
                    echo 4;
                }

            }

        } else {
            echo 3;
        }

        wp_die();

    }

    /**
     * Deletes cached (transient) data.
     *
     * @since   3.0.0
     * @access  private
     */
    private function flush_transients() {

        $recently_transients = get_option( 'recently_transients' );

        if ( $recently_transients && is_array( $recently_transients ) && !empty( $recently_transients ) ) {

            for ( $t=0; $t < count( $recently_transients ); $t++ )
                delete_transient( $recently_transients[$t] );

            update_option( 'recently_transients', array() );

        }

    }

    /**
     * Checks if an upgrade procedure is required.
     *
     * @since	1.0.0
     */
    public function upgrade_check(){
        $this->upgrade_site();
    } // end upgrade_check

    /**
     * Upgrades single site.
     *
     * @since 2.0.0
     */
    private function upgrade_site() {
        // Get Recently version
        $recently_ver = get_option( 'recently_ver' );

        if ( !$recently_ver ) {
            add_option( 'recently_ver', $this->version );
        } elseif ( version_compare( $recently_ver, $this->version, '<' ) ) {
            $this->upgrade();
        }
    }

    /**
     * On plugin upgrade, performs a number of actions: update Recently database tables structures (if needed),
     * run the setup wizard (if needed), and some other checks.
     *
     * @since	2.4.0
     * @access  private
     * @global	object	wpdb
     */
    private function upgrade() {

        $now = Recently_Helper::now();

        // Keep the upgrade process from running too many times
        if ( $recently_update = get_option('recently_update') ) {

            $from_time = strtotime( $recently_update );
            $to_time = strtotime( $now );
            $difference_in_minutes = round( abs( $to_time - $from_time ) / 60, 2 );

            // Upgrade flag is still valid, abort
            if ( $difference_in_minutes <= 15 )
                return;

            // Upgrade flag expired, delete it and continue
            delete_option( 'recently_update' );

        }

        add_option( 'recently_update', $now );

        global $wpdb;

        // Validate the structure of the tables, create missing tables / fields if necessary
        Recently_Activator::track_new_site();

        // Update Recently version
        update_option( 'recently_ver', $this->version );

        // Remove upgrade flag
        delete_option( 'recently_update' );

    } // end __upgrade

    /**
     * Checks if the technical requirements are met.
     *
     * @since	1.0.0
     * @access  private
     * @link	http://wordpress.stackexchange.com/questions/25910/uninstall-activate-deactivate-a-plugin-typical-features-how-to/25979#25979
     * @global	string $wp_version
     * @return	array
     */
    private function check_requirements() {

        global $wp_version;

        $php_min_version = '5.2';
        $wp_min_version = '4.7';
        $php_current_version = phpversion();
        $errors = array();

        if ( version_compare( $php_min_version, $php_current_version, '>' ) ) {
            $errors[] = sprintf(
                __( 'Your PHP installation is too old. Recently requires at least PHP version %1$s to function correctly. Please contact your hosting provider and ask them to upgrade PHP to %1$s or higher.', 'recently' ),
                $php_min_version
            );
        }

        if ( version_compare( $wp_min_version, $wp_version, '>' ) ) {
            $errors[] = sprintf(
                __( 'Your WordPress version is too old. Recently requires at least WordPress version %1$s to function correctly. Please update your blog via Dashboard &gt; Update.', 'recently' ),
                $wp_min_version
            );
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

        printf(
            __('<div class="notice notice-error"><p>%1$s</p><p><i>%2$s</i> has been <strong>deactivated</strong>.</p></div>', 'recently'),
            join( '</p><p>', $errors ),
            'Recently'
        );

        $plugin_file = 'recently/recently.php';
        deactivate_plugins( $plugin_file );

    } // end check_admin_notices

} // End Recently_Admin class
