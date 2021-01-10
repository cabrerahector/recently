<div class="recently-ph"></div>
<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'recently'); ?>:</label><br />
    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e('Show up to', 'recently'); ?>:</label><br />
    <input type="text" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" value="<?php echo $instance['args']['posts_per_page']; ?>" class="widefat" style="width:50px!important" /> <?php _e('posts', 'recently'); ?>
</p>

<p style="margin-bottom:0;">
    <label for="<?php echo $this->get_field_id( 'offset' ); ?>"><?php _e('Offset', 'recently'); ?>:</label><br />
    <input type="text" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" value="<?php echo $instance['args']['offset']; ?>" class="widefat" style="width:50px!important" /><br />
    <small><?php _e('Number of posts to displace or pass over', 'recently'); ?>.</small>
</p>

<br /><hr /><br />

<legend><strong><?php _e('Filters', 'recently'); ?></strong></legend>

<p>
    <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e('Types', 'recently'); ?>:</label>
    <input type="text" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" value="<?php echo implode(',', $instance['args']['post_type']); ?>" class="widefat" /><br />
    <small><?php _e('Post types, separated by comma', 'recently'); ?>.</small>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'post_id' ); ?>"><?php _e('Posts to exclude', 'recently'); ?>:</label>
    <input type="text" id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>" value="<?php echo ( isset($instance['args']['post__not_in']) ) ? implode(',', $instance['args']['post__not_in']) : ''; ?>" class="widefat" /><br />
    <small><?php _e('Post / Page IDs, separated by comma', 'recently'); ?>.</small>
</p>

<p class="taxonomy-filter">
    <label><?php _e('Taxonomy', 'recently'); ?>:</label><br />

    <label><input type="radio" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" value=""<?php echo ( !isset($instance['args']['tax_query']) ) ? ' checked' : ''; ?>> <?php _e('None', 'recently'); ?></label><br>

    <?php
    $taxonomies = get_taxonomies( array('public' => true), 'objects' );

    if ( $taxonomies ) {
        foreach ( $taxonomies  as $taxonomy ) {
            // If the theme doesn't support post formats, do not enable this filter
            if ( !current_theme_supports( 'post-formats' ) && 'post_format' == $taxonomy->name )
                continue;

            echo '<label><input type="radio" name="' . $this->get_field_name( 'taxonomy' ) . '" value="' . $taxonomy->name . '"' . ( (isset($instance['args']['tax_query']) && isset($instance['args']['tax_query'][0]) && $taxonomy->name == $instance['args']['tax_query'][0]['taxonomy']) ? ' checked' : '') . '> ' . $taxonomy->labels->singular_name . '</label><br>';
        }
    }

    $tax_terms = array();

    if ( isset($instance['args']['tax_query']) ) {
        foreach( $instance['args']['tax_query'] as $tax_query ) {
            if ( !is_array($tax_query) ) {
                continue;
            }

            if ( isset($tax_query['terms']) ) {
                for ( $i=0; $i < count($tax_query['terms']); $i++ ) {
                    $tax_terms[] = ( isset($tax_query['operator']) && 'NOT IN' == $tax_query['operator'] ) ? (-1) * $tax_query['terms'][$i] : $tax_query['terms'][$i];
                }
            }
        }
    }
    ?>

    <div class="tax-field" style="display: <?php echo isset($instance['args']['tax_query']) ? 'block' : 'none'; ?>; width: 90%; margin: 10px 0; padding: 3% 5%; background: #f5f5f5;">
        <span class="tax-id-field"<?php echo ( isset($instance['args']['tax_query']) && 'post_format' == $instance['args']['tax_query'][0]['taxonomy'] ) ? ' style="display:none;"' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'tax_id' ); ?>"><?php _e('Term IDs', 'recently'); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id( 'tax_id' ); ?>" name="<?php echo $this->get_field_name( 'tax_id' ); ?>" value="<?php echo implode(',', $tax_terms); ?>" class="widefat" /><br />
            <small><?php _e('Taxonomy IDs, separated by comma (prefix a minus sign to exclude)', 'recently'); ?>.</small>
        </span>

        <span class="tax-slug-field"<?php echo ( isset($instance['args']['tax_query']) && 'post_format' != $instance['args']['tax_query'][0]['taxonomy'] ) ? ' style="display:none;"' : ''; ?>>
            <label for="<?php echo $this->get_field_id( 'tax_slug' ); ?>"><?php _e('Term Slugs', 'recently'); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id( 'tax_slug' ); ?>" name="<?php echo $this->get_field_name( 'tax_slug' ); ?>" value="<?php echo implode(',', $tax_terms); ?>" class="widefat" /><br />
            <small><?php _e('Taxonomy slugs, separated by comma', 'recently'); ?>.</small>
        </span>
    </div>
</p>

<p style="margin-bottom:0;">
    <?php
    $author_ids = array();

    if ( isset($instance['args']['author__in']) || isset($instance['args']['author__not_in']) ) {

        for ( $i=0; $i < count($instance['args']['author__in']); $i++ )
            $author_ids[] = $instance['args']['author__in'][$i];

        for ( $i=0; $i < count($instance['args']['author__not_in']); $i++ )
            $author_ids[] = (-1) * $instance['args']['author__not_in'][$i];

    }
    ?>
    <label for="<?php echo $this->get_field_id( 'author_id' ); ?>"><?php _e('Authors', 'recently'); ?>:</label>
    <input type="text" id="<?php echo $this->get_field_id( 'author_id' ); ?>" name="<?php echo $this->get_field_name( 'author_id' ); ?>" value="<?php echo implode(',', $author_ids); ?>" class="widefat" /><br />
    <small><?php _e('Author IDs, separated by comma (prefix a minus sign to exclude)', 'recently'); ?>.</small>
</p>

<br /><hr /><br />

<legend><strong><?php _e('Posts settings', 'recently'); ?></strong></legend>
<br />

<div style="display:<?php if ( function_exists('the_ratings_results') ) : ?>block<?php else: ?>none<?php endif; ?>;">
    <input type="checkbox" class="checkbox" <?php echo ($instance['rating']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'rating' ); ?>" name="<?php echo $this->get_field_name( 'rating' ); ?>" /> <label for="<?php echo $this->get_field_id( 'rating' ); ?>"><?php _e('Display post rating', 'recently'); ?></label>
</div>

<input type="checkbox" class="checkbox checkbox-shorten-title" <?php echo ($instance['shorten_title']['active']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'shorten_title-active' ); ?>" name="<?php echo $this->get_field_name( 'shorten_title-active' ); ?>" /> <label for="<?php echo $this->get_field_id( 'shorten_title-active' ); ?>"><?php _e('Shorten title', 'recently'); ?></label><br />

<div class="shorten-title" style="display:<?php if ($instance['shorten_title']['active']) : ?>block<?php else: ?>none<?php endif; ?>; width:90%; margin:10px 0; padding:3% 5%; background:#f5f5f5;">
    <label for="<?php echo $this->get_field_id( 'shorten_title-length' ); ?>"><?php _e('Shorten title to', 'recently'); ?> <input type="text" id="<?php echo $this->get_field_id( 'shorten_title-length' ); ?>" name="<?php echo $this->get_field_name( 'shorten_title-length' ); ?>" value="<?php echo $instance['shorten_title']['length']; ?>" class="widefat" style="width:50px!important" /></label><br />
    <label><input type="radio" name="<?php echo $this->get_field_name( 'shorten_title-words' ); ?>" value="0" <?php echo (!isset($instance['shorten_title']['words']) || 0 == $instance['shorten_title']['words']) ? 'checked="checked"' : ''; ?> /> <?php _e('characters', 'recently'); ?></label><br />
    <label><input type="radio" name="<?php echo $this->get_field_name( 'shorten_title-words' ); ?>" value="1" <?php echo (isset($instance['shorten_title']['words']) && $instance['shorten_title']['words']) ? 'checked="checked"' : ''; ?> /> <?php _e('words', 'recently'); ?></label>
</div>

<input type="checkbox" class="checkbox checkbox-excerpt" <?php echo ($instance['post-excerpt']['active']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'post-excerpt-active' ); ?>" name="<?php echo $this->get_field_name( 'post-excerpt-active' ); ?>" /> <label for="<?php echo $this->get_field_id( 'post-excerpt-active' ); ?>"><?php _e('Display post excerpt', 'recently'); ?></label><br />

<div class="excerpt" style="display:<?php if ($instance['post-excerpt']['active']) : ?>block<?php else: ?>none<?php endif; ?>; width:90%; margin:10px 0; padding:3% 5%; background:#f5f5f5;">
    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'post-excerpt-format' ); ?>" name="<?php echo $this->get_field_name( 'post-excerpt-format' ); ?>" <?php echo ($instance['post-excerpt']['keep_format']) ? 'checked="checked"' : ''; ?> /> <label for="<?php echo $this->get_field_id( 'post-excerpt-format' ); ?>"><?php _e('Keep text format and links', 'recently'); ?></label><br /><br />

    <label for="<?php echo $this->get_field_id( 'post-excerpt-length' ); ?>"><?php _e('Excerpt length', 'recently'); ?>: <input type="text" id="<?php echo $this->get_field_id( 'post-excerpt-length' ); ?>" name="<?php echo $this->get_field_name( 'post-excerpt-length' ); ?>" value="<?php echo $instance['post-excerpt']['length']; ?>" class="widefat" style="width:50px!important" /></label><br  />

    <label><input type="radio" name="<?php echo $this->get_field_name( 'post-excerpt-words' ); ?>" value="0" <?php echo (!isset($instance['post-excerpt']['words']) || !$instance['post-excerpt']['words']) ? 'checked="checked"' : ''; ?> /> <?php _e('characters', 'recently'); ?></label><br />
    <label><input type="radio" name="<?php echo $this->get_field_name( 'post-excerpt-words' ); ?>" value="1" <?php echo (isset($instance['post-excerpt']['words']) && $instance['post-excerpt']['words']) ? 'checked="checked"' : ''; ?> /> <?php _e('words', 'recently'); ?></label>
</div>

<input type="checkbox" class="checkbox checkbox-thumbnail" <?php echo ( $instance['thumbnail']['active'] ) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'thumbnail-active' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail-active' ); ?>" /> <label for="<?php echo $this->get_field_id( 'thumbnail-active' ); ?>"><?php _e('Display post thumbnail', 'recently'); ?></label>

<div class="thumbnail" style="display:<?php if ($instance['thumbnail']['active']) : ?>block<?php else: ?>none<?php endif; ?>; width:90%; margin:10px 0; padding:3% 5%; background:#f5f5f5;">
    <label><input type='radio' id='thumbnail-predefined-size' name='<?php echo $this->get_field_name( 'thumbnail-size-source' ); ?>' value='predefined' <?php echo ($instance['thumbnail']['build'] == 'predefined') ? 'checked="checked"' : ''; ?> /><?php _e('Use predefined size', 'recently'); ?></label><br />

    <select id="<?php echo $this->get_field_id( 'thumbnail-size' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail-size' ); ?>" class="widefat" style="margin:5px 0;">
        <?php
        $default_thumbnail_sizes = $this->image->get_sizes();

        foreach ( $default_thumbnail_sizes as $name => $attr ) :
            echo '<option value="' . $name . '"' . ( ($instance['thumbnail']['build'] == 'predefined' && $attr['width'] == $instance['thumbnail']['width'] && $attr['height'] == $instance['thumbnail']['height'] ) ? ' selected="selected"' : '' ) . '>' . $name . ' (' . $attr['width'] . ' x ' . $attr['height'] . ( $attr['crop'] ? ', hard crop' : ', soft crop' ) . ')</option>';
        endforeach;
        ?>
    </select>

    <hr />

    <label><input type='radio' id='thumbnail-manual-size' name='<?php echo $this->get_field_name( 'thumbnail-size-source' ); ?>' value='manual' <?php echo ($instance['thumbnail']['build'] == 'manual') ? 'checked="checked"' : ''; ?> /><?php _e('Set size manually', 'recently'); ?></label><br />

    <label for="<?php echo $this->get_field_id( 'thumbnail-width' ); ?>"><?php _e('Width', 'recently'); ?>:</label>
    <input type="text" id="<?php echo $this->get_field_id( 'thumbnail-width' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail-width' ); ?>" value="<?php echo $instance['thumbnail']['width']; ?>" class="widefat" style="margin:3px 0; width:50px!important" /> px<br />

    <label for="<?php echo $this->get_field_id( 'thumbnail-height' ); ?>"><?php _e('Height', 'recently'); ?>:</label>
    <input type="text" id="<?php echo $this->get_field_id( 'thumbnail-height' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail-height' ); ?>" value="<?php echo $instance['thumbnail']['height']; ?>" class="widefat" style="width:50px!important" /> px
</div><br />

<br /><hr /><br />

<legend><strong><?php _e('Metadata Tag settings', 'recently'); ?></strong></legend><br />

<input type="checkbox" class="checkbox checkbox-comments-count" <?php echo ($instance['meta_tag']['comment_count']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'comment_count' ); ?>" name="<?php echo $this->get_field_name( 'comment_count' ); ?>" /> <label for="<?php echo $this->get_field_id( 'comment_count' ); ?>"><?php _e('Display comment count', 'recently'); ?></label><br />

<input type="checkbox" class="checkbox checkbox-views-count" <?php echo ($instance['meta_tag']['views']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'views' ); ?>" name="<?php echo $this->get_field_name( 'views' ); ?>"<?php echo ( (!function_exists('wpp_get_views') && ! \Recently\Helper::is_plugin_active('wp-postviews/wp-postviews.php') && ! function_exists('get_tptn_post_count_only')) ? ' disabled' : '' ); ?> /> <label for="<?php echo $this->get_field_id( 'views' ); ?>"><?php _e('Display views', 'recently'); ?></label><br />

<input type="checkbox" class="checkbox checkbox-author" <?php echo ($instance['meta_tag']['author']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" /> <label for="<?php echo $this->get_field_id( 'author' ); ?>"><?php _e('Display author', 'recently'); ?></label><br />

<input type="checkbox" class="checkbox checkbox-date" <?php echo ($instance['meta_tag']['date']['active']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" /> <label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Display date', 'recently'); ?></label><br />

<div class="date-formats" style="display:<?php if ($instance['meta_tag']['date']['active']) : ?>block<?php else: ?>none<?php endif; ?>; width:90%; margin:10px 0; padding:3% 5%; background:#f5f5f5;">
    <legend><strong><?php _e('Date Format', 'recently'); ?></strong></legend><br />

    <label><input type='radio' name='<?php echo $this->get_field_name( 'date_format' ); ?>' value='relative' <?php echo ($instance['meta_tag']['date']['format'] == 'relative') ? 'checked="checked"' : ''; ?> /><?php _e('Relative', 'recently'); ?></label><br />
    <label title='<?php echo get_option('date_format'); ?>'><input type='radio' name='<?php echo $this->get_field_name( 'date_format' ); ?>' value='<?php echo get_option('date_format'); ?>' <?php echo ($instance['meta_tag']['date']['format'] == get_option('date_format')) ? 'checked="checked"' : ''; ?> /><?php echo date_i18n(get_option('date_format'), time()); ?></label> <small>(<a href="<?php echo admin_url('options-general.php'); ?>" title="<?php _e('WordPress Date Format', 'recently'); ?>" target="_blank"><?php _e('WordPress Date Format', 'recently'); ?></a>)</small><br />
    <label title='F j, Y'><input type='radio' name='<?php echo $this->get_field_name( 'date_format' ); ?>' value='F j, Y' <?php echo ($instance['meta_tag']['date']['format'] == 'F j, Y') ? 'checked="checked"' : ''; ?> /><?php echo date_i18n('F j, Y', time()); ?></label><br />
    <label title='Y/m/d'><input type='radio' name='<?php echo $this->get_field_name( 'date_format' ); ?>' value='Y/m/d' <?php echo ($instance['meta_tag']['date']['format'] == 'Y/m/d') ? 'checked="checked"' : ''; ?> /><?php echo date_i18n('Y/m/d', time()); ?></label><br />
    <label title='m/d/Y'><input type='radio' name='<?php echo $this->get_field_name( 'date_format' ); ?>' value='m/d/Y' <?php echo ($instance['meta_tag']['date']['format'] == 'm/d/Y') ? 'checked="checked"' : ''; ?> /><?php echo date_i18n('m/d/Y', time()); ?></label><br />
    <label title='d/m/Y'><input type='radio' name='<?php echo $this->get_field_name( 'date_format' ); ?>' value='d/m/Y' <?php echo ($instance['meta_tag']['date']['format'] == 'd/m/Y') ? 'checked="checked"' : ''; ?> /><?php echo date_i18n('d/m/Y', time()); ?></label>
</div>

<input type="checkbox" class="checkbox checkbox-taxonomy" <?php echo ($instance['meta_tag']['taxonomy']['active']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'show_tax' ); ?>" name="<?php echo $this->get_field_name( 'show_tax' ); ?>" /> <label for="<?php echo $this->get_field_id( 'show_tax' ); ?>"><?php _e('Display taxonomy', 'recently'); ?></label><br />

<div class="taxonomies" style="display:<?php if ($instance['meta_tag']['taxonomy']['active']) : ?>block<?php else: ?>none<?php endif; ?>; width:90%; margin:10px 0; padding:3% 5%; background:#f5f5f5;">
<?php
    if ( $taxonomies ) {
        foreach ( $taxonomies  as $taxonomy ) {
            if ( 'post_format' == $taxonomy->name )
                continue;

            echo '<label><input type="checkbox" class="checkbox" name="' . $this->get_field_name( 'taxonomy_name' ) . '[]" value="' . $taxonomy->name . '"' . ( in_array($taxonomy->name, $instance['meta_tag']['taxonomy']['names']) ? ' checked="checked"' : '' ) . '> ' . $taxonomy->labels->name . '</label><br>';
        }
    }
?>
</div>

<br /><hr /><br />

<legend><strong><?php _e('HTML Markup settings', 'recently'); ?></strong></legend><br />

<input type="checkbox" class="checkbox checkbox-custom-html" <?php echo ($instance['markup']['custom_html']) ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'custom_html' ); ?>" name="<?php echo $this->get_field_name( 'custom_html' ); ?>" /> <label for="<?php echo $this->get_field_id( 'custom_html' ); ?>"><?php _e('Use custom HTML Markup', 'recently'); ?></label><br />

<div class="custom-html" style="display:<?php if ($instance['markup']['custom_html']) : ?>block<?php else: ?>none<?php endif; ?>; width:90%; margin:10px 0; padding:3% 5%; background:#f5f5f5;">
    <?php
    $recently_title_start = $instance['markup']['title-start'];
    $recently_title_end = $instance['markup']['title-end'];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title-start' ); ?>"><?php _e('Before / after title', 'recently'); ?>:</label> <br />
        <input type="text" id="<?php echo $this->get_field_id( 'title-start' ); ?>" name="<?php echo $this->get_field_name( 'title-start' ); ?>" value="<?php echo $recently_title_start; ?>" class="widefat" style="width:49%!important" />
        <input type="text" id="<?php echo $this->get_field_id( 'title-end' ); ?>" name="<?php echo $this->get_field_name( 'title-end' ); ?>" value="<?php echo $recently_title_end; ?>" class="widefat" style="width:49%!important" />
    </p>

    <p>
        <label for="<?php echo $this->get_field_id( 'recently-start' ); ?>"><?php _e('Before / after Recently', 'recently'); ?>:</label> <br />
        <input type="text" id="<?php echo $this->get_field_id( 'recently-start' ); ?>" name="<?php echo $this->get_field_name( 'recently-start' ); ?>" value="<?php echo esc_attr($instance['markup']['recently-start']); ?>" class="widefat" style="width:49%!important" />
        <input type="text" id="<?php echo $this->get_field_id( 'recently-end' ); ?>" name="<?php echo $this->get_field_name( 'recently-end' ); ?>" value="<?php echo esc_attr($instance['markup']['recently-end']); ?>" class="widefat" style="width:49%!important" />
    </p>

    <p>
        <label for="<?php echo $this->get_field_id( 'post-html' ); ?>"><?php _e('Post HTML Markup', 'recently'); ?>:</label> <br />
        <textarea class="widefat" rows="10" id="<?php echo $this->get_field_id( 'post-html' ); ?>" name="<?php echo $this->get_field_name( 'post-html' ); ?>"><?php echo $instance['markup']['post-html']; ?></textarea><br>
        <small><?php printf( __('See available <a href="%s" target="_blank">Content Tags</a>', 'recently'), admin_url('options-general.php?page=recently&tab=params') ); ?>.</small>
    </p>

</div>
<br /><br />
