<?php
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) )
    exit( 'Please do not load this page directly' );

$tabs = array(
    'tools' => __( 'Tools', 'recently' ),
    'params' => __( 'Parameters', 'recently' ),
    'debug' => 'Debug'
);

// Set active tab
if ( isset( $_GET['tab'] ) && isset( $tabs[$_GET['tab']] ) )
    $current = $_GET['tab'];
else
    $current = 'tools';

// Update options on form submission
if ( isset($_POST['section']) ) {

    if ( "misc" == $_POST['section'] ) {

        $current = 'tools';

        if ( isset( $_POST['recently-admin-token'] ) && wp_verify_nonce( $_POST['recently-admin-token'], 'recently-update-misc-options' ) ) {

            $this->options['tools']['misc']['include_stylesheet'] = $_POST['css'];

            update_site_option('recently_config', $this->options);
            echo "<div class=\"notice notice-success is-dismissible\"><p><strong>" . __('Settings saved.', 'recently' ) . "</strong></p></div>";

        }

    }
    elseif ( "markup" == $_POST['section'] ) {

        $current = 'tools';

        if ( isset( $_POST['recently-admin-token'] ) && wp_verify_nonce( $_POST['recently-admin-token'], 'recently-update-html-options' ) ) {

            // link attrs
            $this->options['tools']['markup']['link']['attr']['target'] = $_POST['link_target'];
            $this->options['tools']['markup']['link']['attr']['rel'] = $_POST['link_rel'];

            // thumb
            if ($_POST['thumb_source'] == "custom_field" && (!isset($_POST['thumb_field']) || empty($_POST['thumb_field']))) {
                echo '<div id="wpp-message" class="error fade"><p>'.__('Please provide the name of your custom field.', 'recently').'</p></div>';
            } else {
                $this->options['tools']['markup']['thumbnail']['source'] = $_POST['thumb_source'];
                $this->options['tools']['markup']['thumbnail']['field'] = ( !empty( $_POST['thumb_field']) ) ? $_POST['thumb_field'] : '';
                $this->options['tools']['markup']['thumbnail']['default'] = ( !empty( $_POST['upload_thumb_src']) ) ? $_POST['upload_thumb_src'] : "";
                $this->options['tools']['markup']['thumbnail']['resize'] = $_POST['thumb_field_resize'];

                update_site_option('recently_config', $this->options);
                echo "<div class=\"notice notice-success is-dismissible\"><p><strong>" . __('Settings saved.', 'recently' ) . "</strong></p></div>";
            }

        }

    }
    elseif ( "data" == $_POST['section'] ) {

        $current = 'tools';

        if ( isset( $_POST['recently-admin-token'] ) && wp_verify_nonce( $_POST['recently-admin-token'], 'recently-update-data-options' ) ) {

            $this->options['tools']['data']['ajax'] = $_POST['ajax'];

            // if any of the caching settings was updated, destroy all transients created by the plugin
            if ( $this->options['tools']['data']['cache']['active'] != $_POST['cache'] || $this->options['tools']['data']['cache']['interval']['time'] != $_POST['cache_interval_time'] || $this->options['tools']['data']['cache']['interval']['value'] != $_POST['cache_interval_value'] ) {
                $this->flush_transients();
            }

            $this->options['tools']['data']['cache']['active'] = $_POST['cache'];
            $this->options['tools']['data']['cache']['interval']['time'] = $_POST['cache_interval_time'];
            $this->options['tools']['data']['cache']['interval']['value'] = ( isset($_POST['cache_interval_value']) && is_numeric($_POST['cache_interval_value']) && $_POST['cache_interval_value'] > 0 )
              ? $_POST['cache_interval_value']
              : 1;

            update_site_option('recently_config', $this->options);
            echo "<div class=\"notice notice-success is-dismissible\"><p><strong>" . __('Settings saved.', 'recently' ) . "</strong></p></div>";

        }

    }

}

if ( $this->options['tools']['misc']['include_stylesheet'] && !file_exists( get_stylesheet_directory() . '/recently.css' ) ) {
    echo '<div id="recently-message" class="update-nag">'. __( 'Any changes made to Recently\'s default stylesheet will be lost after every plugin update. In order to prevent this from happening, please copy the recently.css file (located at wp-content/plugins/recently/style) into your theme\'s directory', 'recently' ) .'.</div>';
}

$rand = md5( uniqid(rand(), true) );

if ( !$recently_rand = get_option("recently_rand") ) {
    add_option( "recently_rand", $rand );
} else {
    update_option( "recently_rand", $rand );
}

?>
<script type="text/javascript">
    // TOOLS
    function confirm_clear_image_cache() {
        if (confirm("<?php _e("This operation will delete all cached thumbnails and cannot be undone.", 'recently'); ?> \n\n" + "<?php _e("Do you want to continue?", 'recently'); ?>")) {
            jQuery.post(
                ajaxurl,
                {
                    action: 'recently_clear_thumbnail',
                    token: '<?php echo $rand; ?>'
                },
                function(data){
                    var response = "";

                    switch( data ) {
                        case "1":
                            response = "<?php /*translators: Special characters (such as accents) must be replaced with Javascript Octal codes (eg. \341 is the Octal code for small a with acute accent) */ _e( 'Success! All files have been deleted!', 'recently' ); ?>";
                            break;

                        case "2":
                            response = "<?php /*translators: Special characters (such as accents) must be replaced with Javascript Octal codes (eg. \341 is the Octal code for small a with acute accent) */ _e( 'The thumbnail cache is already empty!', 'recently' ); ?>";
                            break;

                        case "3":
                            response = "<?php /*translators: Special characters (such as accents) must be replaced with Javascript Octal codes (eg. \341 is the Octal code for small a with acute accent) */ _e( 'Invalid action.', 'recently' ); ?>";
                            break;

                        case "4":
                            response = "<?php /*translators: Special characters (such as accents) must be replaced with Javascript Octal codes (eg. \341 is the Octal code for small a with acute accent) */ _e( 'Sorry, you do not have enough permissions to do this. Please contact the site administrator for support.', 'recently' ); ?>";
                            break;

                        default:
                            response = "<?php /*translators: Special characters (such as accents) must be replaced with Javascript Octal codes (eg. \341 is the Octal code for small a with acute accent) */ _e( 'Invalid action.', 'recently' ); ?>";
                            break;
                    }

                    alert( response );
                }
            );
        }
    }
</script>

<nav id="recently-menu">
    <ul>
        <li><a href="#" title="<?php esc_attr_e( 'Menu' ); ?>"><span><?php _e( 'Menu' ); ?></span></a></li>
        <li<?php echo ( 'tools' == $current ) ? ' class="current"' : ''; ?>><a href="<?php echo admin_url( 'options-general.php?page=recently&tab=tools' ); ?>" title="<?php esc_attr_e( 'Tools', 'recently' ); ?>"><span><?php _e( 'Tools', 'recently' ); ?></span></a></li>
        <li<?php echo ( 'params' == $current ) ? ' class="current"' : ''; ?>><a href="<?php echo admin_url( 'options-general.php?page=recently&tab=params' ); ?>" title="<?php esc_attr_e( 'Parameters', 'recently' ); ?>"><span><?php _e( 'Parameters', 'recently' ); ?></span></a></li>
    </ul>
</nav>

<div class="recently-wrapper recently-section-<?php echo $current; ?>">

    <div class="recently-header">
        <h2>Recently</h2>
        <h3><?php echo $tabs[$current]; ?></h3>
    </div>

    <!-- Start tools -->
    <div id="recently_tools" class="recently-content" <?php echo ( 'tools' == $current ) ? '' : ' style="display: none;"'; ?>>

        <br /><h2><?php _e("HTML Markup", 'recently'); ?></h2>

        <form action="" method="post">
            <h3 class="wmpp-subtitle"><?php _e("Links", 'recently'); ?></h3>
               <table class="form-table">
                </tbody>
                    <tr valign="top">
                        <th scope="row"><label for="link_target"><?php _e("Open links in", 'recently'); ?>:</label></th>
                        <td>
                            <select name="link_target" id="link_target">
                                <option <?php if ( $this->options['tools']['markup']['link']['attr']['target'] == '_self' ) {?>selected="selected"<?php } ?> value="_self"><?php _e("Current window", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['link']['attr']['target'] == '_blank' ) {?>selected="selected"<?php } ?> value="_blank"><?php _e("New tab/window", 'recently'); ?></option>
                            </select>
                            <br />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="link_rel"><?php _e("REL attribute", 'recently'); ?>:</label></th>
                        <td>
                            <input type="text" id="link_rel" name="link_rel" value="<?php echo esc_attr( $this->options['tools']['markup']['link']['attr']['rel'] ); ?>" />
                            <p class="description"><?php _e("The rel attribute specifies the relationship between the current document and the linked document", 'recently'); ?>.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3 class="wmpp-subtitle"><?php _e("Thumbnails", 'recently'); ?></h3>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="thumb_default"><?php _e("Default thumbnail", 'recently'); ?>:</label></th>
                        <td>
                            <div id="thumb-review">
                                <img src="<?php echo ( $this->options['tools']['markup']['thumbnail']['default'] ) ? str_replace( parse_url( $this->options['tools']['markup']['thumbnail']['default'], PHP_URL_SCHEME ) . ':', '', $this->options['tools']['markup']['thumbnail']['default'] ) : plugins_url() . '/recently/public/images/no_thumb.jpg'; ?>" alt="" border="0" />
                            </div>
                            <input id="upload_thumb_button" type="button" class="button" value="<?php _e( "Upload thumbnail", 'recently' ); ?>" />
                            <input type="hidden" id="upload_thumb_src" name="upload_thumb_src" value="" />
                            <p class="description"><?php _e("How-to: upload (or select) an image, set Size to Full and click on Upload", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="thumb_source"><?php _e("Pick image from", 'recently'); ?>:</label></th>
                        <td>
                            <select name="thumb_source" id="thumb_source">
                                <option <?php if ($this->options['tools']['markup']['thumbnail']['source'] == "featured") {?>selected="selected"<?php } ?> value="featured"><?php _e("Featured image", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['markup']['thumbnail']['source'] == "first_image") {?>selected="selected"<?php } ?> value="first_image"><?php _e("First image on post", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['markup']['thumbnail']['source'] == "first_attachment") {?>selected="selected"<?php } ?> value="first_attachment"><?php _e("First attachment", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['markup']['thumbnail']['source'] == "custom_field") {?>selected="selected"<?php } ?> value="custom_field"><?php _e("Custom field", 'recently'); ?></option>
                            </select>
                            <br />
                            <p class="description"><?php _e("Tell Recently where it should get thumbnails from", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top" <?php if ($this->options['tools']['markup']['thumbnail']['source'] != "custom_field") {?>style="display:none;"<?php } ?> id="row_custom_field">
                        <th scope="row"><label for="thumb_field"><?php _e("Custom field name", 'recently'); ?>:</label></th>
                        <td>
                            <input type="text" id="thumb_field" name="thumb_field" value="<?php echo esc_attr( $this->options['tools']['markup']['thumbnail']['field'] ); ?>" size="10" <?php if ($this->options['tools']['markup']['thumbnail']['source'] != "custom_field") {?>style="display:none;"<?php } ?> />
                        </td>
                    </tr>
                    <tr valign="top" <?php if ($this->options['tools']['markup']['thumbnail']['source'] != "custom_field") {?>style="display:none;"<?php } ?> id="row_custom_field_resize">
                        <th scope="row"><label for="thumb_field_resize"><?php _e("Resize image from Custom field?", 'recently'); ?>:</label></th>
                        <td>
                            <select name="thumb_field_resize" id="thumb_field_resize">
                                <option <?php if ( !$this->options['tools']['markup']['thumbnail']['resize'] ) {?>selected="selected"<?php } ?> value="0"><?php _e("No, I will upload my own thumbnail", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['thumbnail']['resize'] == 1 ) {?>selected="selected"<?php } ?> value="1"><?php _e("Yes", 'recently'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <?php
                    $wp_upload_dir = wp_upload_dir();
                    if ( is_dir( $wp_upload_dir['basedir'] . "/" . 'recently' ) ) :
                    ?>
                    <tr valign="top">
                        <th scope="row"></th>
                        <td>
                            <input type="button" name="wpp-reset-cache" id="wpp-reset-cache" class="button-secondary" value="<?php _e("Empty image cache", 'recently'); ?>" onclick="confirm_clear_image_cache()" />
                            <p class="description"><?php _e("Use this button to clear Recently's thumbnails cache", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <?php
                    endif;
                    ?>
                </tbody>
            </table>

            <input type="hidden" name="section" value="markup" />
            <br /><input type="submit" class="button-primary action" id="btn_th_ops" value="<?php _e("Apply", 'recently'); ?>" name="" />

            <?php wp_nonce_field( 'recently-update-html-options', 'recently-admin-token' ); ?>
        </form>
        <br />
        <p style="display:block; float:none; clear:both">&nbsp;</p>

        <h2><?php _e("Data", 'recently'); ?></h2>
        <form action="" method="post">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="ajax"><?php _e("Ajaxify widget", 'recently'); ?>:</label></th>
                        <td>
                            <select name="ajax" id="ajax">
                                <option <?php if (!$this->options['tools']['data']['ajax']) {?>selected="selected"<?php } ?> value="0"><?php _e("Disabled", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['data']['ajax']) {?>selected="selected"<?php } ?> value="1"><?php _e("Enabled", 'recently'); ?></option>
                            </select>

                            <br />
                            <p class="description"><?php _e("If you are using a caching plugin such as WP Super Cache, enabling this feature will keep the Recently widget from being cached by it", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="cache"><?php _e("Cache Policy", 'recently'); ?>:</label></th>
                        <td>
                            <select name="cache" id="cache">
                                <option <?php if ( !$this->options['tools']['data']['cache']['active'] ) { ?>selected="selected"<?php } ?> value="0"><?php _e("Never cache", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['cache']['active'] ) { ?>selected="selected"<?php } ?> value="1"><?php _e("Enable caching", 'recently'); ?></option>
                            </select>

                            <br />
                            <p class="description"><?php _e("Sets Recently's cache expiration time. Recently can be cached for a specified amount of time. Recommended for large / high traffic sites", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top" <?php if ( !$this->options['tools']['data']['cache']['active'] ) { ?>style="display:none;"<?php } ?> id="cache_refresh_interval">
                        <th scope="row"><label for="cache_interval_value"><?php _e("Refresh cache every", 'recently'); ?>:</label></th>
                        <td>
                            <input name="cache_interval_value" type="text" id="cache_interval_value" value="<?php echo ( isset($this->options['tools']['data']['cache']['interval']['value']) ) ? (int) $this->options['tools']['data']['cache']['interval']['value'] : 1; ?>" class="small-text">
                            <select name="cache_interval_time" id="cache_interval_time">
                                <option <?php if ($this->options['tools']['data']['cache']['interval']['time'] == "minute") {?>selected="selected"<?php } ?> value="minute"><?php _e("Minute(s)", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['data']['cache']['interval']['time'] == "hour") {?>selected="selected"<?php } ?> value="hour"><?php _e("Hour(s)", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['data']['cache']['interval']['time'] == "day") {?>selected="selected"<?php } ?> value="day"><?php _e("Day(s)", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['data']['cache']['interval']['time'] == "week") {?>selected="selected"<?php } ?> value="week"><?php _e("Week(s)", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['data']['cache']['interval']['time'] == "month") {?>selected="selected"<?php } ?> value="month"><?php _e("Month(s)", 'recently'); ?></option>
                                <option <?php if ($this->options['tools']['data']['cache']['interval']['time'] == "year") {?>selected="selected"<?php } ?> value="month"><?php _e("Year(s)", 'recently'); ?></option>
                            </select>
                            <br />
                            <p class="description" style="display:none;" id="cache_too_long"><?php _e("Really? That long?", 'recently'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td colspan="2">
                            <input type="hidden" name="section" value="data" />
                            <input type="submit" class="button-primary action" id="btn_ajax_ops" value="<?php _e("Apply", 'recently'); ?>" name="" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php wp_nonce_field( 'recently-update-data-options', 'recently-admin-token' ); ?>
        </form>
        <br />
        <p style="display:block; float:none; clear:both">&nbsp;</p>

        <h2 class="wmpp-subtitle"><?php _e("Miscellaneous", 'recently'); ?></h2>
        <form action="" method="post">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="css"><?php _e("Use plugin's stylesheet", 'recently'); ?>:</label></th>
                        <td>
                            <select name="css" id="css">
                                <option <?php if ($this->options['tools']['misc']['include_stylesheet']) {?>selected="selected"<?php } ?> value="1"><?php _e("Enabled", 'recently'); ?></option>
                                <option <?php if (!$this->options['tools']['misc']['include_stylesheet']) {?>selected="selected"<?php } ?> value="0"><?php _e("Disabled", 'recently'); ?></option>
                            </select>
                            <br />
                            <p class="description"><?php _e("By default, the plugin includes a stylesheet called recently.css which you can use to style your recent posts listing. If you wish to use your own stylesheet or do not want it to have it included in the header section of your site, use this.", 'recently'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td colspan="2">
                            <input type="hidden" name="section" value="misc" />
                            <input type="submit" class="button-primary action" value="<?php _e("Apply", 'recently'); ?>" name="" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php wp_nonce_field( 'recently-update-misc-options', 'recently-admin-token' ); ?>
        </form>
    </div>
    <!-- End tools -->

    <!-- Start params -->
    <div id="recently_params" class="recently-content" <?php echo ( 'params' == $current ) ? '' : ' style="display: none;"'; ?>>
        <div>
            <p><?php _e('With the following Content Tags you can customize the Recently widget when using the Custom HTML markup option', 'recently') ?>.</p>
            <br />
            <table cellspacing="0" class="wp-list-table widefat fixed posts">
                <thead>
                    <tr>
                        <th class="manage-column column-title"><?php _e('Parameter', 'recently'); ?></th>
                        <th class="manage-column column-title"><?php _e('What it does ', 'recently'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="alternate">
                        <td><strong>{thumb}</strong></td>
                        <td><?php _e('displays thumbnail linked to post/page (requires enabling the thumbnail option)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{thumb_img}</strong></td>
                        <td><?php _e('displays thumbnail image without linking to post/page (requires enabling the thumbnail option)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{title}</strong></td>
                        <td><?php _e('displays linked post/page title', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{summary}</strong></td>
                        <td><?php _e('displays post/page excerpt (requires enabling the excerpt option)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{metadata}</strong></td> <!-- OJO -->
                        <td><?php _e('displays the default Metadata tags', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{rating}</strong></td>
                        <td><?php _e('displays post/page current rating (requires WP-PostRatings installed and enabled)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{score}</strong></td>
                        <td><?php _e('displays post/page current rating as an integer (requires WP-PostRatings installed and enabled)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{url}</strong></td>
                        <td><?php _e('outputs the URL of the post/page', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{text_title}</strong></td>
                        <td><?php _e('displays post/page title, no link', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{author}</strong></td>
                        <td><?php _e('displays linked author name (requires enabling author under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{taxonomy}</strong></td>
                        <td><?php _e('displays linked taxonomy name (requires enabling taxonomy under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{views}</strong></td>
                        <td><?php _e('displays current views count as an integer only (requires enabling views under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{comments}</strong></td>
                        <td><?php _e('displays current comments count as an integer only (requires enabling comments under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{date}</strong></td>
                        <td><?php _e('displays post/page date (requires enabling date under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- End params -->

    <!-- Start debug -->
    <?php
    global $wp_version;

    $my_theme = wp_get_theme();

    $site_plugins = get_plugins();
    $plugin_names = array();

    foreach( $site_plugins as $main_file => $plugin_meta ) :
        if ( !is_plugin_active( $main_file ) )
            continue;
        $plugin_names[] = sanitize_text_field( $plugin_meta['Name'] . ' ' . $plugin_meta['Version'] );
    endforeach;
    ?>
    <div id="recently_debug" <?php echo ( "debug" == $current ) ? '' : ' style="display: none;"'; ?>>
        <p><strong>PHP version:</strong> <?php echo phpversion(); ?></p>
        <p><strong>PHP extensions:</strong> <?php echo implode( ', ', get_loaded_extensions() ); ?></p>
        <p><strong>WordPress version:</strong> <?php echo $wp_version; ?></p>
        <p><strong>Multisite:</strong> <?php echo ( function_exists( 'is_multisite' ) && is_multisite() ) ? 'Yes' : 'No'; ?></p>
        <p><strong>Active plugins:</strong> <?php echo implode( ', ', $plugin_names ); ?></p>
        <p><strong>Theme:</strong> <?php echo $my_theme->get( 'Name' ) . ' (' . $my_theme->get( 'Version' ) . ') by ' . $my_theme->get( 'Author' ); ?></p>
    </div>
    <!-- End debug -->

</div>