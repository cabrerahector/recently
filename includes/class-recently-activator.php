<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cabrerahector.com
 * @since      2.0.0
 *
 * @package    Recently
 * @subpackage Recently/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Recently
 * @subpackage Recently/includes
 * @author     Hector Cabrera <me@cabrerahector.com>
 */
class Recently_Activator {

    /**
     * Fired when the plugin is activated.
     *
     * @since    1.0.0
     * @param    bool    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
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
                    self::plugin_activate();
                }

                // switch back to current blog
                switch_to_blog( $original_blog_id );

                return;

            }

        }

        self::plugin_activate();

    } // end activate

    /**
     * When a new MU site is added, run its set up actions.
     *
     * @since    2.0.0
     */
    public static function track_new_site() {
        self::plugin_activate();
    } // end track_new_site

    /**
     * On plugin activation, run set up actions.
     *
     * @since    2.0.0
     * @global   object   wpdb
     */
    private static function plugin_activate() {

        // Get Recently version
        $recently_ver = get_option( 'recently_ver' );

        if (
            !$recently_ver
            || version_compare( $recently_ver, RECENTLY_VER, '<' )
        ) {
            // @TODO
        }

    } // end plugin_activate

} // end Recently_Activator class
