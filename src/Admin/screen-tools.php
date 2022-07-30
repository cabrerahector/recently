<?php
if ( 'tools' == $current ) :
    ?>
    <!-- Start tools -->
    <div id="recently_tools" class="recently-content">

        <br /><h2><?php _e("HTML Markup", 'recently'); ?></h2>

        <form action="" method="post">
            <h3 class="wmpp-subtitle"><?php _e("Links", 'recently'); ?></h3>
            <table class="form-table">
                </tbody>
                    <tr valign="top">
                        <th scope="row"><label for="link_target"><?php _e("Open links in", 'recently'); ?>:</label></th>
                        <td>
                            <select name="link_target" id="link_target">
                                <option <?php if ( $this->options['tools']['markup']['link']['attr']['target'] == '_self' ) { ?>selected="selected"<?php } ?> value="_self"><?php _e("Current window", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['link']['attr']['target'] == '_blank' ) { ?>selected="selected"<?php } ?> value="_blank"><?php _e("New tab/window", 'recently'); ?></option>
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
                            <?php
                            $fallback_thumbnail_url = trim($this->options['tools']['markup']['thumbnail']['default']);

                            if ( ! $fallback_thumbnail_url )
                                $fallback_thumbnail_url = $this->image->get_default_url();

                            $fallback_thumbnail_url = str_replace(
                                parse_url(
                                    $fallback_thumbnail_url
                                    , PHP_URL_SCHEME
                                ) . ':',
                                '',
                                $fallback_thumbnail_url
                            );
                            ?>
                            <div id="thumb-review">
                                <img src="<?php echo esc_url($fallback_thumbnail_url); ?>" alt="" />
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
                                <option <?php if ( $this->options['tools']['markup']['thumbnail']['source'] == "featured" ) { ?>selected="selected"<?php } ?> value="featured"><?php _e("Featured image", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['thumbnail']['source'] == "first_image" ) { ?>selected="selected"<?php } ?> value="first_image"><?php _e("First image on post", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['thumbnail']['source'] == "first_attachment" ) { ?>selected="selected"<?php } ?> value="first_attachment"><?php _e("First attachment", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['thumbnail']['source'] == "custom_field" ) { ?>selected="selected"<?php } ?> value="custom_field"><?php _e("Custom field", 'recently'); ?></option>
                            </select>
                            <br />
                            <p class="description"><?php _e("Tell Recently where it should get thumbnails from", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="thumb_lazy_load"><?php _e("Lazy load", 'recently'); ?>:</label></th>
                        <td>
                            <select name="thumb_lazy_load" id="thumb_lazy_load">
                                <option <?php if ( ! $this->options['tools']['markup']['thumbnail']['lazyload'] ) { ?>selected="selected"<?php } ?> value="0"><?php _e("No", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['thumbnail']['lazyload'] ) { ?>selected="selected"<?php } ?> value="1"><?php _e("Yes", 'recently'); ?></option>
                            </select>

                            <p class="description"><?php _e("Set to No to load thumbnails right away or choose Yes to load them only when in view.", 'recently'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top" <?php if ($this->options['tools']['markup']['thumbnail']['source'] != "custom_field") {?>style="display:none;"<?php } ?> id="row_custom_field">
                        <th scope="row"><label for="thumb_field"><?php _e("Custom field name", 'recently'); ?>:</label></th>
                        <td>
                            <input type="text" id="thumb_field" name="thumb_field" value="<?php echo esc_attr( $this->options['tools']['markup']['thumbnail']['field'] ); ?>" size="10" <?php if ( $this->options['tools']['markup']['thumbnail']['source'] != "custom_field" ) { ?>style="display:none;"<?php } ?> />
                        </td>
                    </tr>
                    <tr valign="top" <?php if ( $this->options['tools']['markup']['thumbnail']['source'] != "custom_field" ) { ?>style="display:none;"<?php } ?> id="row_custom_field_resize">
                        <th scope="row"><label for="thumb_field_resize"><?php _e("Resize image from Custom field?", 'recently'); ?>:</label></th>
                        <td>
                            <select name="thumb_field_resize" id="thumb_field_resize">
                                <option <?php if ( ! $this->options['tools']['markup']['thumbnail']['resize'] ) { ?>selected="selected"<?php } ?> value="0"><?php _e("No, I will upload my own thumbnail", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['markup']['thumbnail']['resize'] == 1 ) { ?>selected="selected"<?php } ?> value="1"><?php _e("Yes", 'recently'); ?></option>
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
                            <input type="button" name="recently-reset-cache" id="recently-reset-cache" class="button-secondary" value="<?php _e("Empty image cache", 'recently'); ?>" onclick="confirm_clear_image_cache()" />
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
                                <option <?php if ( ! $this->options['tools']['data']['ajax'] ) { ?>selected="selected"<?php } ?> value="0"><?php _e("Disabled", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['ajax'] ) { ?>selected="selected"<?php } ?> value="1"><?php _e("Enabled", 'recently'); ?></option>
                            </select>

                            <br />
                            <p class="description"><?php _e("If you are using a caching plugin such as WP Super Cache, enabling this feature will keep the Recently widget from being cached by it", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="cache"><?php _e("Data Caching", 'recently'); ?>:</label></th>
                        <td>
                            <select name="cache" id="cache">
                                <option <?php if ( ! $this->options['tools']['data']['cache']['active'] ) { ?>selected="selected"<?php } ?> value="0"><?php _e("Never cache", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['cache']['active'] ) { ?>selected="selected"<?php } ?> value="1"><?php _e("Enable caching", 'recently'); ?></option>
                            </select>

                            <br />
                            <p class="description"><?php _e("Recently can cache the recent posts list for a specified amount of time. Recommended for large / high traffic sites", 'recently'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top" <?php if ( !$this->options['tools']['data']['cache']['active'] ) { ?>style="display:none;"<?php } ?> id="cache_refresh_interval">
                        <th scope="row"><label for="cache_interval_value"><?php _e("Refresh cache every", 'recently'); ?>:</label></th>
                        <td>
                            <input name="cache_interval_value" type="text" id="cache_interval_value" value="<?php echo ( isset($this->options['tools']['data']['cache']['interval']['value']) ) ? (int) $this->options['tools']['data']['cache']['interval']['value'] : 1; ?>" class="small-text">
                            <select name="cache_interval_time" id="cache_interval_time">
                                <option <?php if ( $this->options['tools']['data']['cache']['interval']['time'] == "minute" ) { ?>selected="selected"<?php } ?> value="minute"><?php _e("Minute(s)", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['cache']['interval']['time'] == "hour" ) { ?>selected="selected"<?php } ?> value="hour"><?php _e("Hour(s)", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['cache']['interval']['time'] == "day" ) { ?>selected="selected"<?php } ?> value="day"><?php _e("Day(s)", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['cache']['interval']['time'] == "week" ) { ?>selected="selected"<?php } ?> value="week"><?php _e("Week(s)", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['cache']['interval']['time'] == "month" ) { ?>selected="selected"<?php } ?> value="month"><?php _e("Month(s)", 'recently'); ?></option>
                                <option <?php if ( $this->options['tools']['data']['cache']['interval']['time'] == "year" ) { ?>selected="selected"<?php } ?> value="month"><?php _e("Year(s)", 'recently'); ?></option>
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
                                <option <?php if ( $this->options['tools']['misc']['include_stylesheet'] ) { ?>selected="selected"<?php } ?> value="1"><?php _e("Enabled", 'recently'); ?></option>
                                <option <?php if ( ! $this->options['tools']['misc']['include_stylesheet'] ) { ?>selected="selected"<?php } ?> value="0"><?php _e("Disabled", 'recently'); ?></option>
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
    <?php
endif;
