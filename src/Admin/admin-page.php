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

            $this->options['tools']['misc']['include_stylesheet'] = (int) $_POST['css'];

            update_site_option('recently_config', $this->options);
            echo "<div class=\"notice notice-success is-dismissible\"><p><strong>" . __('Settings saved.', 'recently') . "</strong></p></div>";

        }

    }
    elseif ( "markup" == $_POST['section'] ) {

        $current = 'tools';

        if ( isset( $_POST['recently-admin-token'] ) && wp_verify_nonce( $_POST['recently-admin-token'], 'recently-update-html-options' ) ) {

            // link attrs
            $this->options['tools']['markup']['link']['attr']['target'] = sanitize_text_field($_POST['link_target']);
            $this->options['tools']['markup']['link']['attr']['rel'] = sanitize_text_field($_POST['link_rel']);

            // thumb
            if ($_POST['thumb_source'] == "custom_field" && (!isset($_POST['thumb_field']) || empty($_POST['thumb_field']))) {
                echo '<div id="recently-message" class="error fade"><p>' . __('Please provide the name of your custom field.', 'recently') . '</p></div>';
            } else {
                $this->options['tools']['markup']['thumbnail']['source'] = sanitize_text_field( $_POST['thumb_source'] );
                $this->options['tools']['markup']['thumbnail']['field'] = ( ! empty( $_POST['thumb_field']) ) ? sanitize_text_field($_POST['thumb_field']) : '';
                $this->options['tools']['markup']['thumbnail']['default'] = ( ! empty( $_POST['upload_thumb_src']) ) ? $_POST['upload_thumb_src'] : "";
                $this->options['tools']['markup']['thumbnail']['resize'] = $_POST['thumb_field_resize'];
                $this->options['tools']['markup']['thumbnail']['lazyload'] = (bool) $_POST['thumb_lazy_load'];

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
        <?php if ( current_user_can('edit_others_posts') ) : ?>
            <li <?php echo ( 'tools' == $current ) ? ' class="current"' : ''; ?>><a href="<?php echo admin_url( 'options-general.php?page=recently&tab=tools' ); ?>" title="<?php esc_attr_e( 'Tools', 'recently' ); ?>"><span><?php _e( 'Tools', 'recently' ); ?></span></a></li>
        <?php endif ;?>
        <li <?php echo ( 'params' == $current ) ? ' class="current"' : ''; ?>><a href="<?php echo admin_url( 'options-general.php?page=recently&tab=params' ); ?>" title="<?php esc_attr_e( 'Parameters', 'recently' ); ?>"><span><?php _e( 'Parameters', 'recently' ); ?></span></a></li>
    </ul>
</nav>

<div class="recently-wrapper recently-section-<?php echo esc_attr($current); ?>">

    <div class="recently-header">
        <h2>Recently</h2>
        <h3><?php echo esc_html($tabs[$current]); ?></h3>
    </div>

    <?php
    // Tools
    require_once 'screen-tools.php';
    // Params
    require_once 'screen-params.php';
    // Debug
    require_once 'screen-debug.php';
    ?>

</div>