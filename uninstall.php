<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Recently
 * @author    Hector Cabrera <me@cabrerahector.com>
 * @license   GPL-2.0+
 * @link      http://cabrerahector.com
 * @copyright 2015-2019 Hector Cabrera
 */

// If uninstall, not called from WordPress, then exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Run uninstall for each blog in the network
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	
	global $wpdb;
	
	$original_blog_id = get_current_blog_id();
	$blogs_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

	foreach( $blogs_ids as $blog_id ) {
		
		switch_to_blog( $blog_id );
		
		// Delete plugin's options
		delete_site_option( 'recently_ver' );
		delete_site_option( 'recently_config' );
		delete_site_option( 'recently_rand' );
		delete_site_option( 'recently_transients' );
		
		// delete thumbnails cache and its directory
		delete_thumb_cache();

	}

	// Switch back to current blog
	switch_to_blog( $original_blog_id );

} else {
	
	// Delete plugin's options
	delete_option( 'recently_ver' );
	delete_option( 'recently_config' );
	delete_option( 'recently_rand' );
	delete_option( 'recently_transients' );
	
	// delete thumbnails cache and its directory
	delete_thumb_cache();

}

function delete_thumb_cache() {
	$wp_upload_dir = wp_upload_dir();
				
	if ( is_dir( $wp_upload_dir['basedir'] . "/recently" ) ) {
		$files = glob( $wp_upload_dir['basedir'] . "/recently/*" ); // get all file names
		
		if ( is_array($files) && !empty($files) ) {					
			foreach($files as $file){ // iterate files
				if ( is_file($file) )
					@unlink($file); // delete file
			}
		}
		
		// Finally, delete recently's upload directory
		@rmdir( $wp_upload_dir['basedir'] . "/recently" );
	
	}
}
