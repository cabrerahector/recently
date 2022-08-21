<?php
if ( 'debug' == $current ) {

    if ( ! current_user_can('edit_others_posts') ) {
        echo '<p style="text-align: center;">' . __('Sorry, you do not have enough permissions to do this. Please contact the site administrator for support.', 'recently') . '</p>';
    }
    else {
        ?>
        <!-- Start debug -->
        <?php
        global $wp_version;

        $my_theme = wp_get_theme();

        $site_plugins = get_plugins();
        $plugin_names = array();

        foreach( $site_plugins as $main_file => $plugin_meta ) :
            if ( ! is_plugin_active( $main_file ) )
                continue;
            $plugin_names[] = sanitize_text_field( $plugin_meta['Name'] . ' ' . $plugin_meta['Version'] );
        endforeach;
        ?>
        <div id="recently_debug">
            <p><strong>PHP version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>PHP extensions:</strong> <?php echo implode( ', ', get_loaded_extensions() ); ?></p>
            <p><strong>WordPress version:</strong> <?php echo $wp_version; ?></p>
            <p><strong>Multisite:</strong> <?php echo ( function_exists( 'is_multisite' ) && is_multisite() ) ? 'Yes' : 'No'; ?></p>
            <p><strong>Active plugins:</strong> <?php echo implode( ', ', $plugin_names ); ?></p>
            <p><strong>Theme:</strong> <?php echo $my_theme->get( 'Name' ) . ' (' . $my_theme->get( 'Version' ) . ') by ' . $my_theme->get( 'Author' ); ?></p>
        </div>
        <!-- End debug -->
        <?php
    }
}
