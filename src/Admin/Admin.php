<?php
/**
 * The admin-facing functionality of the plugin.
 *
 * Defines hooks to enqueue the admin-specific stylesheet and JavaScript,
 * plugin settings and other admin stuff.
 *
 * @package    Recently
 * @subpackage Recently/Admin
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Admin;

use Recently\{ Helper, Image, Output };

class Admin {

    /**
     * Slug of the plugin screen.
     *
     * @since   2.0.0
     * @var     string
     */
    protected $screen_hook_suffix = NULL;

    /**
     * Plugin options.
     *
     * @since   2.0.0
     * @var     array      $options
     */
    private $options;

    /**
     * Image object
     *
     * @since   3.0.0
     * @var     Recently\Image
     */
    private $image;

    /**
     * Construct.
     *
     * @since   2.0.0
     * @param   array               $config     Admin settings.
     * @param   \Recently\Image     $image      Image class.
     */
    public function __construct(array $config, Image $image)
    {
        $this->options = $config;
        $this->image = $image;
    }

    /**
     * WordPress public-facing hooks.
     *
     * @since   3.0.0
     */
    public function hooks()
    {
        // Hook fired when a new blog is activated on WP Multisite
        add_action('wpmu_new_blog', [$this, 'activate_new_site']);
        // Load Recently's admin styles and scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        // Add admin screen
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        // Contextual help
        add_action('admin_head', [$this, 'add_contextual_help']);
        // Add plugin settings link
        add_filter('plugin_action_links', [$this, 'add_plugin_settings_link'], 10, 2);
        // Empty plugin's images cache
        add_action('wp_ajax_recently_clear_thumbnail', [$this, 'clear_thumbnails']);
        // Flush cached thumbnail on featured image change/deletion
        add_action('updated_post_meta', [$this, 'updated_post_meta'], 10, 4);
        add_action('deleted_post_meta', [$this, 'deleted_post_meta'], 10, 4);
        // Thickbox setup
        add_action('admin_init', [$this, 'thickbox_setup']);
    }

    /**
     * Fired when a new blog is activated on WP Multisite.
     *
     * @since    2.0.0
     * @param    int      $blog_id    New blog ID
     */
    public function activate_new_site($blog_id)
    {
        if ( 1 !== did_action('wpmu_new_blog') )
            return;

        // run activation for the new blog
        switch_to_blog($blog_id);
        \Recently\Activation\Activator::track_new_site();
        // switch back to current blog
        restore_current_blog();
    }

    /**
     * Enqueues admin facing assets.
     *
     * @since   2.0.0
     */
    public function enqueue_assets()
    {
        if ( ! isset($this->screen_hook_suffix) ) {
            return;
        }

        $screen = get_current_screen();

        if ( isset($screen->id) ) {

            if ( $screen->id == $this->screen_hook_suffix ) {
                wp_enqueue_style('font-awesome', plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/admin/css/vendor/font-awesome.min.css', array(), '4.7.0', 'all');
                wp_enqueue_style('recently-admin-styles', plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/admin/css/admin.css', array(), RECENTLY_VERSION, 'all');

                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_register_script('recently-admin-script', plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/admin/js/admin.js', array('jquery'), RECENTLY_VERSION, true);
                wp_localize_script('recently-admin-script', 'recently_admin_params', array(
                    'nonce' => wp_create_nonce('recently_admin_nonce')
                ));
                wp_enqueue_script('recently-admin-script');
            }

            if ( $screen->id == 'widgets' ) {
                wp_enqueue_script(
                    'recently-admin-widget-script',
                    plugin_dir_url(dirname(dirname(__FILE__))) . 'assets/admin/js/widget-admin.js',
                    array('jquery'),
                    RECENTLY_VERSION,
                    true
                );
            }
        }
    }

    /**
     * Hooks into getttext to change upload button text when uploader is called by Recently.
     *
     * @since   2.0.0
     */
    public function thickbox_setup()
    {
        global $pagenow;

        if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
            add_filter('gettext', array($this, 'replace_thickbox_text'), 1, 3);
        }
    }

    /**
     * Replaces upload button text when uploader is called by Recently.
     *
     * @since   2.0.0
     * @param   string  $translated_text
     * @param   string  $text
     * @param   string  $domain
     * @return  string
     */
    public function replace_thickbox_text($translated_text, $text, $domain)
    {
        if ( 'Insert into Post' == $text ) {
            $referer = strpos(wp_get_referer(), 'recently_admin');
            if ( $referer != '' ) {
                return __('Upload', 'recently');
            }
        }

        return $translated_text;
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu()
    {
        $this->screen_hook_suffix = add_options_page(
            'Recently',
            'Recently',
            'edit_published_posts',
            'recently',
            array($this, 'display_plugin_admin_page')
        );
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page()
    {
        include_once plugin_dir_path(__FILE__) . 'admin-page.php';
    }

    /**
     * Adds contextual help menu.
     *
     * @since   2.0.0
     */
    public function add_contextual_help()
    {
        //get the current screen object
        $screen = get_current_screen();

        if ( isset($screen->id) && $screen->id == $this->screen_hook_suffix ){
            $screen->add_help_tab(
                array(
                    'id'        => 'recently_help_overview',
                    'title'     => __('Overview', 'recently'),
                    'content'   => "<p>" . __("Welcome to Recently's Dashboard! In this screen you will tools to further tweak Recently to your needs and more!", "recently") . "</p>"
                )
            );
            $screen->add_help_tab(
                array(
                    'id'        => 'recently_help_donate',
                    'title'     => __('Like this plugin?', 'recently'),
                    'content'   => '
                        <p style="text-align: center;">' . __('Each donation motivates me to keep releasing free stuff for the WordPress community!', 'recently') . '</p>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="margin: 0; padding: 0; text-align: center;">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="PASXEM2E7JUVC">
                            <input type="image" src="//www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style="display: inline; margin: 0;">
                            <img alt="" border="0" src="//www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form>
                        <p style="text-align: center;">' . sprintf(__('You can <a href="%s" target="_blank">leave a review</a>, too!', 'recently'), 'https://wordpress.org/support/view/plugin-reviews/recently?rate=5#postform') . '</p>'
                )
            );

            // Help sidebar
            $screen->set_help_sidebar(
                sprintf(
                    __('<p><strong>For more information:</strong></p><ul><li><a href="%1$s">Documentation</a></li><li><a href="%2$s">Support</a></li></ul>', 'recently'),
                    "https://github.com/cabrerahector/recently/",
                    "https://wordpress.org/support/plugin/recently/"
                )
            );
        }
    }

    /**
     * Registers Settings link on plugin description.
     *
     * @since   1.0.0
     * @param   array   $links
     * @param   string  $file
     * @return  array
     */
    public function add_plugin_settings_link($links, $file)
    {
        $plugin_file = 'recently/recently.php';

        if (
            is_plugin_active($plugin_file)
            && $plugin_file == $file
        ) {
            array_unshift(
                $links,
                '<a href="' . admin_url('options-general.php?page=recently') . '">' . __('Settings') . '</a>',
                '<a href="https://wordpress.org/support/plugin/recently/">' . __('Support', 'recently') . '</a>'
            );
        }

        return $links;
    }

    /**
     * Truncates thumbnails cache on demand.
     *
     * @since   1.0.0
     * @global  object  $wpdb
     */
    public function clear_thumbnails()
    {
        $recently_uploads_dir = $this->image->get_plugin_uploads_dir();

        if ( is_array($recently_uploads_dir) && ! empty($recently_uploads_dir) ) {
            $token = isset($_POST['token']) ? $_POST['token'] : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- This is a nonce
            $key = get_option("recently_rand");

            if (
                current_user_can('edit_published_posts')
                && ( $token === $key )
            ) {
                if ( is_dir($recently_uploads_dir['basedir']) ) {
                    $files = glob("{$recently_uploads_dir['basedir']}/*"); // get all related images

                    if ( is_array($files) && ! empty($files) ) {
                        foreach( $files as $file ){ // iterate files
                            if ( is_file($file) ) {
                                @unlink($file); // delete file
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

        wp_die();
    }

    /**
     * Fires immediately after deleting metadata of a post.
     *
     * @since 2.0.0
     *
     * @param int    $meta_id    Metadata ID.
     * @param int    $post_id    Post ID.
     * @param string $meta_key   Meta key.
     * @param mixed  $meta_value Meta value.
     */
    public function updated_post_meta($meta_id, $post_id, $meta_key, $meta_value)
    {
        if ( '_thumbnail_id' == $meta_key ) {
            $this->flush_post_thumbnail($post_id);
        }
    }

    /**
     * Fires immediately after deleting metadata of a post.
     *
     * @since 2.0.0
     *
     * @param array  $meta_ids   An array of deleted metadata entry IDs.
     * @param int    $post_id    Post ID.
     * @param string $meta_key   Meta key.
     * @param mixed  $meta_value Meta value.
     */
    public function deleted_post_meta($meta_ids, $post_id, $meta_key, $meta_value)
    {
        if ( '_thumbnail_id' == $meta_key ) {
            $this->flush_post_thumbnail($post_id);
        }
    }

    /**
     * Flushes post's cached thumbnail(s).
     *
     * @since    2.0.0
     *
     * @param    integer    $post_id     Post ID
     */
    public function flush_post_thumbnail($post_id)
    {
        $recently_uploads_dir = $this->image->get_plugin_uploads_dir();

        if ( is_array($recently_uploads_dir) && ! empty($recently_uploads_dir) ) {
            $files = glob("{$recently_uploads_dir['basedir']}/{$post_id}-*.*"); // get all related images

            if ( is_array($files) && ! empty($files) ) {
                foreach( $files as $file ){ // iterate files
                    if ( is_file($file) ) {
                        @unlink($file); // delete file
                    }
                }
            }
        }
    }

    /**
     * Deletes cached (transient) data.
     *
     * @since   2.0.0
     * @access  private
     */
    private function flush_transients()
    {
        $recently_transients = get_option('recently_transients');

        if (
            is_array($recently_transients ) 
            && ! empty( $recently_transients )
        ) {
            for ( $t=0; $t < count($recently_transients); $t++ )
                delete_transient($recently_transients[$t]);

            update_option('recently_transients', array());
        }
    }
}
