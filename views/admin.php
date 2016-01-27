<?php
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__))
	exit('Please do not load this page directly');

// Set active tab
if ( isset($_GET['tab']) )
	$current = $_GET['tab'];
else
	$current = 'tools';

// Update options on form submission
if ( isset($_POST['section']) ) {
	
	if ( "misc" == $_POST['section'] ) {
		
		$current = 'tools';
		
		if ( isset( $_POST['recently-admin-token'] ) && wp_verify_nonce( $_POST['recently-admin-token'], 'recently-update-misc-options' ) ) {
		
			$this->user_settings['tools']['misc']['include_stylesheet'] = $_POST['css'];
			
			update_site_option('recently_config', $this->user_settings);
			echo "<div class=\"updated\"><p><strong>" . __('Settings saved.', $this->plugin_slug ) . "</strong></p></div>";
		
		}
		
	}	
	elseif ( "markup" == $_POST['section'] ) {
		
		$current = 'tools';
		
		if ( isset( $_POST['recently-admin-token'] ) && wp_verify_nonce( $_POST['recently-admin-token'], 'recently-update-html-options' ) ) {
		
			// link attrs
			$this->user_settings['tools']['markup']['link']['attr']['target'] = $_POST['link_target'];
			$this->user_settings['tools']['markup']['link']['attr']['rel'] = $_POST['link_rel'];
			
			// thumb
			if ($_POST['thumb_source'] == "custom_field" && (!isset($_POST['thumb_field']) || empty($_POST['thumb_field']))) {
				echo '<div id="wpp-message" class="error fade"><p>'.__('Please provide the name of your custom field.', $this->plugin_slug).'</p></div>';
			} else {				
				$this->user_settings['tools']['markup']['thumbnail']['source'] = $_POST['thumb_source'];
				$this->user_settings['tools']['markup']['thumbnail']['field'] = ( !empty( $_POST['thumb_field']) ) ? $_POST['thumb_field'] : '';
				$this->user_settings['tools']['markup']['thumbnail']['default'] = ( !empty( $_POST['upload_thumb_src']) ) ? $_POST['upload_thumb_src'] : "";
				$this->user_settings['tools']['markup']['thumbnail']['resize'] = $_POST['thumb_field_resize'];
				
				update_site_option('recently_config', $this->user_settings);				
				echo "<div class=\"updated\"><p><strong>" . __('Settings saved.', $this->plugin_slug ) . "</strong></p></div>";
			}
		
		}
		
	}
	elseif ( "data" == $_POST['section'] ) {
		
		$current = 'tools';
		
		if ( isset( $_POST['recently-admin-token'] ) && wp_verify_nonce( $_POST['recently-admin-token'], 'recently-update-data-options' ) ) {
		
			$this->user_settings['tools']['data']['ajax'] = $_POST['ajax'];
			
			// if any of the caching settings was updated, destroy all transients created by the plugin
			if ( $this->user_settings['tools']['data']['cache']['active'] != $_POST['cache'] || $this->user_settings['tools']['data']['cache']['interval']['time'] != $_POST['cache_interval_time'] || $this->user_settings['tools']['data']['cache']['interval']['value'] != $_POST['cache_interval_value'] ) {
				$this->__flush_transients();
			}
			
			$this->user_settings['tools']['data']['cache']['active'] = $_POST['cache'];			
			$this->user_settings['tools']['data']['cache']['interval']['time'] = $_POST['cache_interval_time'];
			$this->user_settings['tools']['data']['cache']['interval']['value'] = ( isset($_POST['cache_interval_value']) && is_numeric($_POST['cache_interval_value']) && $_POST['cache_interval_value'] > 0 ) 
			  ? $_POST['cache_interval_value']
			  : 1;
			
			update_site_option('recently_config', $this->user_settings);
			echo "<div class=\"updated\"><p><strong>" . __('Settings saved.', $this->plugin_slug ) . "</strong></p></div>";	
		
		}
			
	}
		
}

if ( $this->user_settings['tools']['misc']['include_stylesheet'] && !file_exists( get_stylesheet_directory() . '/recently.css' ) ) {
	echo '<div id="recently-message" class="update-nag">'. __('Any changes made to Recently\'s default stylesheet will be lost after every plugin update. In order to prevent this from happening, please copy the recently.css file (located at wp-content/plugins/recently/style) into your theme\'s directory', $this->plugin_slug) .'.</div>';
}

$rand = md5(uniqid(rand(), true));
$recently_rand = get_site_option("recently_rand");	

if ( empty($recently_rand) ) {	
	add_site_option( "recently_rand", $rand );
} else {
	update_site_option( "recently_rand", $rand );
}

$recently_rand = $rand;

?>
<script type="text/javascript">
	// TOOLS
	function confirm_clear_image_cache() {
		if (confirm("<?php _e("This operation will delete all cached thumbnails and cannot be undone.", $this->plugin_slug); ?> \n\n" + "<?php _e("Do you want to continue?", $this->plugin_slug); ?>")) {
			jQuery.post(ajaxurl, {action: 'recently_clear_thumbnail', token: '<?php echo $recently_rand; ?>'}, function(response){
				alert( response );
			});
		}
	}
	
	jQuery(document).ready(function($){
		$('.recently_boxes:visible').css({
			display: 'inline',
			float: 'left'
		}).width( $('.recently_boxes:visible').parent().width() - $('.recently_box').outerWidth() - 15 );
		
		$(window).on('resize', function(){
			$('.recently_boxes:visible').css({
				display: 'inline',
				float: 'left'
			}).width( $('.recently_boxes:visible').parent().width() - $('.recently_box').outerWidth() - 15 );
		});
	});
</script>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2>Recently</h2>
    
    <h2 class="nav-tab-wrapper">
    <?php
    // build tabs    
    $tabs = array(         
		'tools' => __('Tools', $this->plugin_slug),
		'params' => __('Parameters', $this->plugin_slug),
		'about' => __('About', $this->plugin_slug)
    );
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=recently&tab=$tab'>$name</a>";
    }    
    ?>
    </h2>
    
    <!-- Start tools -->
    <div id="recently_tools" class="recently_boxes"<?php if ( "tools" == $current ) {?> style="display:block;"<?php } ?>>
    
    	<br /><h2><?php _e("HTML Markup", $this->plugin_slug); ?></h2>
        
        <form action="" method="post">
        	<h3 class="wmpp-subtitle"><?php _e("Links", $this->plugin_slug); ?></h3>
           	<table class="form-table">
            	</tbody>
            		<tr valign="top">
                        <th scope="row"><label for="link_target"><?php _e("Open links in", $this->plugin_slug); ?>:</label></th>
                        <td>
                            <select name="link_target" id="link_target">
                                <option <?php if ( $this->user_settings['tools']['markup']['link']['attr']['target'] == '_self' ) {?>selected="selected"<?php } ?> value="_self"><?php _e("Current window", $this->plugin_slug); ?></option>
                                <option <?php if ( $this->user_settings['tools']['markup']['link']['attr']['target'] == '_blank' ) {?>selected="selected"<?php } ?> value="_blank"><?php _e("New tab/window", $this->plugin_slug); ?></option>
                            </select>
                            <br />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="link_rel"><?php _e("REL attribute", $this->plugin_slug); ?>:</label></th>
                        <td>
                            <input type="text" id="link_rel" name="link_rel" value="<?php echo esc_attr( $this->user_settings['tools']['markup']['link']['attr']['rel'] ); ?>" />
                            <p class="description"><?php _e("The rel attribute specifies the relationship between the current document and the linked document", $this->plugin_slug); ?>.</p>
                        </td>
                    </tr>
            	</tbody>
            </table>
            
        	<h3 class="wmpp-subtitle"><?php _e("Thumbnails", $this->plugin_slug); ?></h3>
            <table class="form-table">
                <tbody>
                	<tr valign="top">
                        <th scope="row"><label for="thumb_default"><?php _e("Default thumbnail", $this->plugin_slug); ?>:</label></th>
                        <td>                        	
                            <div id="thumb-review">
                                <img src="<?php echo $this->user_settings['tools']['markup']['thumbnail']['default']; ?>" alt="" border="0" />
                            </div>
                            <input id="upload_thumb_button" type="button" class="button" value="<?php _e( "Upload thumbnail", $this->plugin_slug ); ?>" />
                            <input type="hidden" id="upload_thumb_src" name="upload_thumb_src" value="" />
                            <p class="description"><?php _e("How-to: upload (or select) an image, set Size to Full and click on Upload", $this->plugin_slug); ?>.</p>
                        </td>
                    </tr>                    
                    <tr valign="top">
                        <th scope="row"><label for="thumb_source"><?php _e("Pick image from", $this->plugin_slug); ?>:</label></th>
                        <td>
                            <select name="thumb_source" id="thumb_source">
                                <option <?php if ($this->user_settings['tools']['markup']['thumbnail']['source'] == "featured") {?>selected="selected"<?php } ?> value="featured"><?php _e("Featured image", $this->plugin_slug); ?></option>
                                <option <?php if ($this->user_settings['tools']['markup']['thumbnail']['source'] == "first_image") {?>selected="selected"<?php } ?> value="first_image"><?php _e("First image on post", $this->plugin_slug); ?></option>
                                <option <?php if ($this->user_settings['tools']['markup']['thumbnail']['source'] == "custom_field") {?>selected="selected"<?php } ?> value="custom_field"><?php _e("Custom field", $this->plugin_slug); ?></option>
                            </select>
                            <br />
                            <p class="description"><?php _e("Tell Recently where it should get thumbnails from", $this->plugin_slug); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top" <?php if ($this->user_settings['tools']['markup']['thumbnail']['source'] != "custom_field") {?>style="display:none;"<?php } ?> id="row_custom_field">
                        <th scope="row"><label for="thumb_field"><?php _e("Custom field name", $this->plugin_slug); ?>:</label></th>
                        <td>
                            <input type="text" id="thumb_field" name="thumb_field" value="<?php echo esc_attr( $this->user_settings['tools']['markup']['thumbnail']['field'] ); ?>" size="10" <?php if ($this->user_settings['tools']['markup']['thumbnail']['source'] != "custom_field") {?>style="display:none;"<?php } ?> />
                        </td>
                    </tr>
                    <tr valign="top" <?php if ($this->user_settings['tools']['markup']['thumbnail']['source'] != "custom_field") {?>style="display:none;"<?php } ?> id="row_custom_field_resize">
                        <th scope="row"><label for="thumb_field_resize"><?php _e("Resize image from Custom field?", $this->plugin_slug); ?>:</label></th>
                        <td>
                            <select name="thumb_field_resize" id="thumb_field_resize">
                                <option <?php if ( !$this->user_settings['tools']['markup']['thumbnail']['resize'] ) {?>selected="selected"<?php } ?> value="0"><?php _e("No, I will upload my own thumbnail", $this->plugin_slug); ?></option>
                                <option <?php if ( $this->user_settings['tools']['markup']['thumbnail']['resize'] == 1 ) {?>selected="selected"<?php } ?> value="1"><?php _e("Yes", $this->plugin_slug); ?></option>                        
                            </select>
                        </td>
                    </tr>
                    <?php
					$wp_upload_dir = wp_upload_dir();					
					if ( is_dir( $wp_upload_dir['basedir'] . "/" . $this->plugin_slug ) ) :
					?>
                    <tr valign="top">
                        <th scope="row"></th>
                        <td>                        	
                            <input type="button" name="wpp-reset-cache" id="wpp-reset-cache" class="button-secondary" value="<?php _e("Empty image cache", $this->plugin_slug); ?>" onclick="confirm_clear_image_cache()" />                            
                            <p class="description"><?php _e("Use this button to clear Recently's thumbnails cache", $this->plugin_slug); ?>.</p>
                        </td>
                    </tr>
                    <?php
					endif;
					?>
                </tbody>
            </table>
            
            <input type="hidden" name="section" value="markup" />
            <br /><input type="submit" class="button-secondary action" id="btn_th_ops" value="<?php _e("Apply", $this->plugin_slug); ?>" name="" />
            
            <?php wp_nonce_field( 'recently-update-html-options', 'recently-admin-token' ); ?>
        </form>
        <br />
        <p style="display:block; float:none; clear:both">&nbsp;</p>
                
        <h2><?php _e("Data", $this->plugin_slug); ?></h2>
        <form action="" method="post">
        	<table class="form-table">
                <tbody>                	
                    <tr valign="top">
                        <th scope="row"><label for="ajax"><?php _e("Ajaxify widget", $this->plugin_slug); ?>:</label></th>
                        <td>
                            <select name="ajax" id="ajax">                                
                                <option <?php if (!$this->user_settings['tools']['data']['ajax']) {?>selected="selected"<?php } ?> value="0"><?php _e("Disabled", $this->plugin_slug); ?></option>
                                <option <?php if ($this->user_settings['tools']['data']['ajax']) {?>selected="selected"<?php } ?> value="1"><?php _e("Enabled", $this->plugin_slug); ?></option>
                            </select>
                    
                            <br />
                            <p class="description"><?php _e("If you are using a caching plugin such as WP Super Cache, enabling this feature will keep the Recently widget from being cached by it", $this->plugin_slug); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="cache"><?php _e("Cache Expiry Policy", $this->plugin_slug); ?>:</label> <small>[<a href="https://github.com/cabrerahector/wordpress-popular-posts/wiki/7.-Performance#caching" target="_blank" title="<?php _e('What is this?', $this->plugin_slug); ?>">?</a>]</small></th>
                        <td>
                            <select name="cache" id="cache">
                                <option <?php if ( !$this->user_settings['tools']['data']['cache']['active'] ) { ?>selected="selected"<?php } ?> value="0"><?php _e("Never cache", $this->plugin_slug); ?></option>
                                <option <?php if ( $this->user_settings['tools']['data']['cache']['active'] ) { ?>selected="selected"<?php } ?> value="1"><?php _e("Enable caching", $this->plugin_slug); ?></option>
                            </select>
                    
                            <br />
                            <p class="description"><?php _e("Sets Recently's cache expiration time. Recently can be cached for a specified amount of time. Recommended for large / high traffic sites", $this->plugin_slug); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top" <?php if ( !$this->user_settings['tools']['data']['cache']['active'] ) { ?>style="display:none;"<?php } ?> id="cache_refresh_interval">
                        <th scope="row"><label for="cache_interval_value"><?php _e("Refresh cache every", $this->plugin_slug); ?>:</label></th>
                        <td>
                        	<input name="cache_interval_value" type="text" id="cache_interval_value" value="<?php echo ( isset($this->user_settings['tools']['data']['cache']['interval']['value']) ) ? (int) $this->user_settings['tools']['data']['cache']['interval']['value'] : 1; ?>" class="small-text">
                            <select name="cache_interval_time" id="cache_interval_time">
                            	<option <?php if ($this->user_settings['tools']['data']['cache']['interval']['time'] == "minute") {?>selected="selected"<?php } ?> value="minute"><?php _e("Minute(s)", $this->plugin_slug); ?></option>
                                <option <?php if ($this->user_settings['tools']['data']['cache']['interval']['time'] == "hour") {?>selected="selected"<?php } ?> value="hour"><?php _e("Hour(s)", $this->plugin_slug); ?></option>
                                <option <?php if ($this->user_settings['tools']['data']['cache']['interval']['time'] == "day") {?>selected="selected"<?php } ?> value="day"><?php _e("Day(s)", $this->plugin_slug); ?></option>                                
                                <option <?php if ($this->user_settings['tools']['data']['cache']['interval']['time'] == "week") {?>selected="selected"<?php } ?> value="week"><?php _e("Week(s)", $this->plugin_slug); ?></option>
                                <option <?php if ($this->user_settings['tools']['data']['cache']['interval']['time'] == "month") {?>selected="selected"<?php } ?> value="month"><?php _e("Month(s)", $this->plugin_slug); ?></option>
                                <option <?php if ($this->user_settings['tools']['data']['cache']['interval']['time'] == "year") {?>selected="selected"<?php } ?> value="month"><?php _e("Year(s)", $this->plugin_slug); ?></option>
                            </select>                            
                            <br />
                            <p class="description" style="display:none;" id="cache_too_long"><?php _e("Really? That long?", $this->plugin_slug); ?></p>
                        </td>
                    </tr>                    
                    <tr valign="top">                            	
                        <td colspan="2">
                            <input type="hidden" name="section" value="data" />
                    		<input type="submit" class="button-secondary action" id="btn_ajax_ops" value="<?php _e("Apply", $this->plugin_slug); ?>" name="" />
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php wp_nonce_field( 'recently-update-data-options', 'recently-admin-token' ); ?>
        </form>
        <br />
        <p style="display:block; float:none; clear:both">&nbsp;</p>
        
        <h2 class="wmpp-subtitle"><?php _e("Miscellaneous", $this->plugin_slug); ?></h2>
        <form action="" method="post">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="css"><?php _e("Use plugin's stylesheet", $this->plugin_slug); ?>:</label></th>
                        <td>
                            <select name="css" id="css">
                                <option <?php if ($this->user_settings['tools']['misc']['include_stylesheet']) {?>selected="selected"<?php } ?> value="1"><?php _e("Enabled", $this->plugin_slug); ?></option>
                                <option <?php if (!$this->user_settings['tools']['misc']['include_stylesheet']) {?>selected="selected"<?php } ?> value="0"><?php _e("Disabled", $this->plugin_slug); ?></option>
                            </select>
                            <br />
                            <p class="description"><?php _e("By default, the plugin includes a stylesheet called recently.css which you can use to style your recent posts listing. If you wish to use your own stylesheet or do not want it to have it included in the header section of your site, use this.", $this->plugin_slug); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td colspan="2">
                            <input type="hidden" name="section" value="misc" />
                            <input type="submit" class="button-secondary action" value="<?php _e("Apply", $this->plugin_slug); ?>" name="" />
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php wp_nonce_field( 'recently-update-misc-options', 'recently-admin-token' ); ?>
        </form>        
    </div>
    <!-- End tools -->
    
    <!-- Start params -->
    <div id="recently_params" class="recently_boxes"<?php if ( "params" == $current ) {?> style="display:block;"<?php } ?>>        
        <div>
            <p><?php _e('With the following Content Tags you can customize the Recently widget when using the Custom HTML markup option', $this->plugin_slug) ?>.</p>
            <br />
            <table cellspacing="0" class="wp-list-table widefat fixed posts">
                <thead>
                    <tr>
                        <th class="manage-column column-title"><?php _e('Parameter', $this->plugin_slug); ?></th>
                        <th class="manage-column column-title"><?php _e('What it does ', $this->plugin_slug); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="alternate">
                        <td><strong>{thumb}</strong></td>
                        <td><?php _e('displays thumbnail linked to post/page (requires enabling the thumbnail option)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{thumb_img}</strong></td>
                        <td><?php _e('displays thumbnail image without linking to post/page (requires enabling the thumbnail option)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{title}</strong></td>
                        <td><?php _e('displays linked post/page title', $this->plugin_slug); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{summary}</strong></td>
                        <td><?php _e('displays post/page excerpt (requires enabling the excerpt option)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{metadata}</strong></td> <!-- OJO -->
                        <td><?php _e('displays the default Metadata tags', $this->plugin_slug); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{rating}</strong></td>
                        <td><?php _e('displays post/page current rating (requires WP-PostRatings installed and enabled)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{score}</strong></td>
                        <td><?php _e('displays post/page current rating as an integer (requires WP-PostRatings installed and enabled)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{url}</strong></td>
                        <td><?php _e('outputs the URL of the post/page', $this->plugin_slug); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{text_title}</strong></td>
                        <td><?php _e('displays post/page title, no link', $this->plugin_slug); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{author}</strong></td>
                        <td><?php _e('displays linked author name (requires enabling author under Metadata Tags)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{taxonomy}</strong></td>
                        <td><?php _e('displays linked taxonomy name (requires enabling taxonomy under Metadata Tags)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{views}</strong></td>
                        <td><?php _e('displays current views count as an integer only (requires enabling views under Metadata Tags)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{comments}</strong></td>
                        <td><?php _e('displays current comments count as an integer only (requires enabling comments under Metadata Tags)', $this->plugin_slug); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{date}</strong></td>
                        <td><?php _e('displays post/page date (requires enabling date under Metadata Tags)', $this->plugin_slug); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- End params -->
    
    <!-- Start about -->
    <div id="recently_faq" class="recently_boxes"<?php if ( "about" == $current ) {?> style="display:block;"<?php } ?>>
        
        <h3><?php /*translators: %s is a placeholder for version number, don't forget to include it! */ echo sprintf( __('About Recently %s', $this->plugin_slug), $this->version); ?></h3>
        <p><?php _e( 'This version includes the following changes', $this->plugin_slug ); ?>:</p>
        
        <ul>
            <li>Initial release.</li>
        </ul>
                
    </div>
    <!-- End about -->
    
    <div id="recently_donate" class="recently_box" style="">
        <h3 style="margin-top:0; text-align:center;"><?php _e('Do you like this plugin?', $this->plugin_slug); ?></h3>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="PASXEM2E7JUVC">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style="display:block; margin:0 auto;">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
        <p><?php _e( 'Each donation motivates me to keep releasing free stuff for the WordPress community!', $this->plugin_slug ); ?></p>
        <p><?php /*translators: %s is a placeholder for the Review form URL, don't forget to include it! */ echo sprintf( __('You can <a href="%s" target="_blank">leave a review</a>, too!', $this->plugin_slug), 'https://wordpress.org/support/view/plugin-reviews/recently?rate=5#postform' ); ?></p>
    </div>
    
    <div id="recently_support" class="recently_box" style="">
        <h3 style="margin-top:0; text-align:center;"><?php _e('Need help?', $this->plugin_slug); ?></h3>
        <p><?php /*translators: %s is a placeholder for the forum URL, don't forget to include it! */ echo sprintf( __('Visit <a href="%s" target="_blank">the forum</a> for support, questions and feedback.', $this->plugin_slug), 'https://wordpress.org/support/plugin/recently' ); ?></p>
        <p><?php _e('Let\'s make this plugin even better!', $this->plugin_slug); ?></p>
    </div>
        
</div>