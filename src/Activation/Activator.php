<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://cabrerahector.com
 * @since      1.0.0
 *
 * @package    Recently
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Recently
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Activation;

class Activator {
    /**
     * Fired when the plugin is activated.
     *
     * @since    1.0.0
     * @param    bool    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
     * @global   object  $wpdb
     */
    public static function activate($network_wide)
    {
        global $wpdb;

        if ( function_exists('is_multisite') && \is_multisite() ) {
            // run activation for each blog in the network
            if ( $network_wide ) {
                $original_blog_id = \get_current_blog_id();
                $blogs_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");

                foreach( $blogs_ids as $blog_id ) {
                    \switch_to_blog($blog_id);
                    self::plugin_activate();
                }

                // switch back to current blog
                \switch_to_blog($original_blog_id);

                return;
            }
        }

        self::plugin_activate();
    }

    /**
     * Runs when a new MU site is added.
     *
     * @since    1.0.0
     */
    public static function track_new_site()
    {
        self::plugin_activate();
    }

    /**
     * Updates / saves plugin version to the database.
     *
     * @since    1.0.0
     * @global   object   $wpdb
     */
    private static function plugin_activate()
    {
        $recently_ver = \get_option('recently_ver');

        if (
            ! $recently_ver
            || version_compare($recently_ver, RECENTLY_VERSION, '<')
        ) {
            \update_option('recently_ver', RECENTLY_VERSION);
        }
    }
}
