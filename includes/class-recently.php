<?php

class Recently {

    /**
     * The unique identifier of this plugin.
     *
     * @since    2.0.0
     * @access   protected
     * @var      string     $plugin_name
     */
    protected $plugin_name;

    /**
     * The current version of this plugin.
     *
     * @since    2.0.0
     * @access   protected
     * @var      string     $version
     */
    protected $version;

    /**
     * Constructor.
     *
     * @since    2.0.0
     */
    public function __construct(){

        $this->plugin_name = 'recently';
        $this->version = RECENTLY_VER;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Loads the required dependencies for this plugin.
     *
     * @since    2.0.0
     * @access   private
     */
    private function load_dependencies(){

        /**
         * The class responsible for defining internationalization functionality of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-i18n.php';

        /**
         * The class responsible for translating objects.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-translate.php';

        /**
         * Settings class.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-settings.php';

        /**
         * Helper class.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-helper.php';

        /**
         * The class responsible for handling the actions and filters of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-loader.php';

        /**
         * The class responsible for creating images.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-image.php';

        /**
         * The class responsible for building the HTML output.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-output.php';

        /**
         * The widget class.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recently-widget.php';

        /**
         * The class responsible for defining all actions that occur on the admin-facing side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-recently-admin.php';

        /**
         * The class responsible for defining all actions that occur on the public-facing side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-recently-public.php';

        /**
         * Get loader.
         */
        $this->loader = new Recently_Loader();

    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     *
     * @since    2.0.0
     * @access   private
     */
    public function define_admin_hooks() {

        $plugin_admin = new Recently_Admin( $this->get_plugin_name(), $this->get_version() );

        // Check admin notices
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'check_admin_notices' );
        // Upgrade check
        $this->loader->add_action( 'init', $plugin_admin, 'upgrade_check' );
        // Hook fired when a new blog is activated on WP Multisite
        $this->loader->add_action( 'wpmu_new_blog', $plugin_admin, 'activate_new_site' );
        // Hook fired when a blog is deleted on WP Multisite
        $this->loader->add_filter( 'wpmu_drop_tables', $plugin_admin, 'delete_site_data', 10, 2 );
        // Load Recently's admin styles and scripts
        $this->loader->add_action( 'admin_print_styles', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        // Add admin screen
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        // Thickbox setup
        $this->loader->add_action( 'admin_init', $plugin_admin, 'thickbox_setup' );
        // Contextual help
        $this->loader->add_action( 'admin_head', $plugin_admin, 'add_contextual_help' );
        // Add plugin settings link
        $this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'add_plugin_settings_link', 10, 2 );
        // Empty plugin's images cache
        $this->loader->add_action( 'wp_ajax_recently_clear_thumbnail', $plugin_admin, 'clear_thumbnails' );
        // Flush cached thumbnail on featured image change
        $this->loader->add_action( 'update_postmeta', $plugin_admin, 'flush_post_thumbnail', 10, 4 );
        // Initialize widget
        $this->loader->add_action( 'widgets_init', $plugin_admin, 'register_widget' );

    }

    /**
     * Register all of the hooks related to the public area functionality of the plugin.
     *
     * @since    2.0.0
     * @access   private
     */
    public function define_public_hooks() {

        $plugin_public = new Recently_Public( $this->get_plugin_name(), $this->get_version() );

        // Add WPP's stylesheet and scripts
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    2.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Recently_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     2.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     2.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    2.0.0
     */
    public function run() {
        $this->loader->run();
    }

} // End Recently class
