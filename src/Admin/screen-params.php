<?php
if ( 'params' == $current ) :
    ?>
    <!-- Start params -->
    <div id="recently_params" class="recently-content">
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
                        <td><strong>{title_attr}</strong></td>
                        <td><?php _e('similar to text_title, sanitized for use in title/alt attributes', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{summary}</strong></td>
                        <td><?php _e('displays post/page excerpt (requires enabling the excerpt option)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{meta}</strong></td>
                        <td><?php _e('displays the default Metadata tags', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{rating}</strong></td>
                        <td><?php _e('displays post/page current rating (requires WP-PostRatings installed and enabled)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{score}</strong></td>
                        <td><?php _e('displays post/page current rating as an integer (requires WP-PostRatings installed and enabled)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{url}</strong></td>
                        <td><?php _e('outputs the URL of the post/page', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{text_title}</strong></td>
                        <td><?php _e('displays post/page title, no link', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{author}</strong></td>
                        <td><?php _e('displays linked author name (requires enabling author under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{taxonomy}</strong></td>
                        <td><?php _e('displays linked taxonomy name (requires enabling taxonomy under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{views}</strong></td>
                        <td><?php _e('displays current views count as an integer only (requires enabling views under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{comments}</strong></td>
                        <td><?php _e('displays current comments count as an integer only (requires enabling comments under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{date}</strong></td>
                        <td><?php _e('displays post/page date (requires enabling date under Metadata Tags)', 'recently'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>{total_items}</strong></td>
                        <td><?php _e('outputs number of recent posts found', 'recently'); ?></td>
                    </tr>
                    <tr class="alternate">
                        <td><strong>{item_position}</strong></td>
                        <td><?php _e('outputs the position of the post in the listing', 'recently'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- End params -->
    <?php
endif;
