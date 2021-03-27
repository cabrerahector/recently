<?php
namespace Recently;

class Output {

    /**
     * WP_Query instance.
     *
     * @since   2.0.0
     * @var     \WP_Query
     */
    private $recents;

    /**
     * HTML content.
     *
     * @since   2.0.0
     * @var     string
     */
    private $output;

    /**
     * Widget / shortcode settings.
     *
     * @since   2.0.0
     * @var     array
     */
    private $options;

    /**
     * Administrative settings.
     *
     * @since   2.0.0
     * @var     array
     */
    private $admin_options = array();

    /**
     * Default thumbnail sizes
     *
     * @since   2.0.0
     * @var     array
     */
    private $default_thumbnail_sizes = array();

    /**
     * Recently_Image object
     *
     * @since   2.0.0
     * @var     \Recently\Image
     */
    private $image;

    /**
     * Translate object.
     *
     * @var     \Recenty\Translate
     * @since   2.0.0
     */
    private $translate;

    /**
     * Themer object.
     *
     * @since   3.0.0
     * @var     \Recently\Themer
     */
    private $themer;

    /**
     * Construct
     *
     * @since   2.0.0
     * @param   array               $public_options
     * @param   array               $admin_options
     * @param   \Recently\Image     $image
     * @param   \Recently\Translate $translate
     * @param   \Recently\Themer    $themer
     */
    public function __construct(array $public_options, array $admin_options, Image $image, Translate $translate, Themer $themer)
    {
        $this->options = $public_options;
        $this->admin_options = $admin_options;
        $this->image = $image;
        $this->translate = $translate;
        $this->themer = $themer;

        $this->default_thumbnail_sizes = $this->image->get_sizes();
    }

    /**
     * Build the HTML output.
     *
     * @since   2.0.0
     */
    public function build_output()
    {
        // Items found, render them
        if ( $this->recents->have_posts() ) {

            $this->options = Helper::merge_array_r(
                Settings::$defaults['widget_options'],
                $this->options
            );

            // Allow WP themers / coders access to raw data
            // so they can build their own output
            if ( has_filter('recently_custom_html') ) {
                $this->output = apply_filters('recently_custom_html', $this->recents, $this->options);
                return;
            }

            if (
                isset($this->options['theme']['name'])
                && $this->options['theme']['name']
            ) {
                $this->output .= '<div class="recently-sr">';

                if ( @file_exists(get_template_directory() . '/recently/themes/' . $this->options['theme']['name'] . '/style.css') ) {
                    $theme_stylesheet = get_template_directory() . '/recently/themes/' . $this->options['theme']['name'] . '/style.css';
                } else {
                    $theme_stylesheet = $this->themer->get_theme($this->options['theme']['name'])['path'] . '/style.css';
                }

                $theme_css_rules = wp_strip_all_tags(file_get_contents($theme_stylesheet), true);
                $additional_styles = '';

                if ( has_filter('recently_additional_theme_styles') ) {
                    $additional_styles = wp_strip_all_tags(apply_filters('recently_additional_theme_styles', '', $this->options['theme']['name']), true);

                    if ( $additional_styles )
                        $additional_styles = ' /* additional rules */ ' . $additional_styles;
                }

                $this->output .= '<style>' . $theme_css_rules . $additional_styles . '</style>';
            }

            /* Open HTML wrapper */
            // Output a custom wrapper
            if (
                isset($this->options['markup']['custom_html'])
                && $this->options['markup']['custom_html']
                && isset($this->options['markup']['recently-start'])
                && isset($this->options['markup']['recently-end'])
            ){
                $this->output .= "\n" . htmlspecialchars_decode($this->options['markup']['recently-start'], ENT_QUOTES) ."\n";
            }
            // Output the default wrapper
            else {
                $classes = "recently-list";

                if ( $this->options['thumbnail']['active'] )
                    $classes .= " recently-list-with-thumbnails";

                $this->output .= "\n" . "<ul class=\"{$classes}\">" . "\n";
            }

            $position = 0;

            // Loop through items
            while ( $this->recents->have_posts() ) {
                $position++;
                $this->recents->the_post();
                $this->output .= $this->render_post($position);
            }

            /* Close HTML wrapper */
            // Output a custom wrapper
            if (
                isset($this->options['markup']['custom_html'])
                && $this->options['markup']['custom_html']
                && isset($this->options['markup']['recently-start'])
                && isset($this->options['markup']['recently-end'])
            ){
                $this->output .= "\n" . htmlspecialchars_decode($this->options['markup']['recently-end'], ENT_QUOTES) ."\n";
            }
            // Output default wrapper
            else {
                $this->output .= "</ul>" . "\n";
            }

            if (
                isset($this->options['theme']['name'])
                && $this->options['theme']['name']
            ) {
                $this->output .= '</div>';
            }

        } // Nothing to display
        else {
            $this->output = apply_filters('recently_no_data', "<p class=\"recently-no-data\">" . __('Sorry, nothing to show yet.', 'recently') . "</p>");
        }

        // Restore original Post Data
        wp_reset_postdata();
    }

    /**
     * Build the HTML markup for a single post.
     *
     * @since   2.0.0
     * @access  private
     * @param   int     $position
     * @return  string
     */
    private function render_post($position = 1)
    {
        global $post;

        $content = '';
        $postID = $post->ID;

        // Permalink
        $permalink = $this->get_permalink($postID);

        // Thumbnail
        $post_thumbnail = $this->get_thumbnail($postID);

        // Post title (and title attribute)
        $post_title_attr = esc_attr(wp_strip_all_tags($this->get_title()));
        $post_title = $this->get_title();

        if ( $this->options['shorten_title']['active'] ) {
            $length = ( filter_var($this->options['shorten_title']['length'], FILTER_VALIDATE_INT) && $this->options['shorten_title']['length'] > 0 )
              ? $this->options['shorten_title']['length']
              : 25;

            $post_title = force_balance_tags(Helper::truncate( $post_title, $length, $this->options['shorten_title']['words']));
        }

        // Post excerpt
        $post_excerpt = $this->get_excerpt();

        // Post rating
        $post_rating = $this->get_rating();

        /**
         * Post meta
         */

        // Post date
        $post_date = $this->get_date();

        // Post taxonomies
        $post_taxonomies = $this->get_taxonomies();

        // Post author
        $post_author = $this->get_author();

        // Post views count
        $post_views = $this->get_pageviews();

        // Post comments count
        $post_comments = $this->get_comments();

        // Post meta
        $post_meta = join(' | ', $this->get_metadata($post_comments, $post_views, $post_author, $post_date, $post_taxonomies));

        // Build custom HTML output
        if ( $this->options['markup']['custom_html'] ) {

            $data = array(
                'id' => $post->ID,
                'title' => '<a href="' . $permalink . '" ' . ($post_title_attr !== $post_title ? 'title="' . $post_title_attr . '" ' : '' ) . 'class="recently-post-title" target="' . $this->admin_options['tools']['markup']['link']['attr']['target'] . '" rel="' . esc_attr($this->admin_options['tools']['markup']['link']['attr']['rel']) . '">' . $post_title . '</a>',
                'title_attr' => $post_title_attr,
                'summary' => $post_excerpt,
                'stats' => $post_meta,
                'img' => ( ! empty($post_thumbnail) ) ? '<a href="' . $permalink . '"' . ($post_title_attr !== $post_title ? 'title="' . $post_title_attr . '" ' : '' ) . 'target="' . $this->admin_options['tools']['markup']['link']['attr']['target'] . '" rel="' . esc_attr($this->admin_options['tools']['markup']['link']['attr']['rel']) . '" class="recently-thumbnail-wrapper">' . $post_thumbnail . '</a>' : '',
                'img_no_link' => $post_thumbnail,
                'url' => $permalink,
                'text_title' => $post_title,
                'taxonomy' => $post_taxonomies,
                'author' => ( !empty($post_author) ) ? '<a href="' . get_author_posts_url( $post->post_author ) . '">' . $post_author . '</a>' : '',
                'views' => number_format_i18n( $post_views ),
                'comments' => number_format_i18n( $post_comments ),
                'date' => $post_date,
                'total_items' => $this->recents->found_posts,
                'item_position' => $position
            );

            $content = $this->format_content(
                htmlspecialchars_decode($this->options['markup']['post-html'], ENT_QUOTES),
                $data,
                $this->options['rating']
            ) . "\n";

        } // Use the "stock" HTML output
        else {
            $is_single = Helper::is_single();

            $post_thumbnail = ( ! empty($post_thumbnail) )
              ? "<a href=\"{$permalink}\" " . ($post_title_attr !== $post_title ? "title=\"{$post_title_attr}\" " : "") . "target=\"{$this->admin_options['tools']['markup']['link']['attr']['target']}\" rel=\"" . esc_attr($this->admin_options['tools']['markup']['link']['attr']['rel']) . "\" class=\"recently-thumbnail-wrapper\">{$post_thumbnail}</a>\n"
              : "";

            $post_excerpt = ( ! empty($post_excerpt) )
              ? " <span class=\"recently-excerpt\">{$post_excerpt}</span>\n"
              : "";

            $post_meta = ( ! empty($post_meta) )
              ? " <span class=\"recently-meta post-stats\">{$post_meta}</span>\n"
              : '';

            $post_rating = ( ! empty($post_rating) )
              ? " <span class=\"recently-rating\">{$post_rating}</span>\n"
              : "";

            $recently_post_class = array();

            if ( $is_single == $postID ) {
                $recently_post_class[] = "current";
            }

            // Allow themers / plugin developer
            // to add custom classes to each post
            $recently_post_class = apply_filters("recently_post_class", $recently_post_class, $postID);

            $content =
                "<li" . ( ( is_array($recently_post_class) && ! empty($recently_post_class) ) ? ' class="' . esc_attr( implode(" ", $recently_post_class) ) . '"' : '' ) . ">\n"
                . $post_thumbnail
                . "<a href=\"{$permalink}\" " . ($post_title_attr !== $post_title ? "title=\"{$post_title_attr}\" " : "") . "class=\"recently-post-title\" target=\"{$this->admin_options['tools']['markup']['link']['attr']['target']}\" rel=\"" . esc_attr($this->admin_options['tools']['markup']['link']['attr']['rel']) . "\">{$post_title}</a>\n"
                . $post_excerpt
                . $post_meta
                . $post_rating
                . "</li>\n";
        }

        return apply_filters('recently_post', $content, $post, $this->options);
    }

    /**
     * Return the permalink.
     * 
     * @since   2.0.0
     * @access  private
     * @return  string
     */
    private function get_permalink()
    {
        global $post;

        $trid = $this->translate->get_object_id($post->ID, get_post_type($post->ID));

        if ( $post->ID != $trid ) {
            return get_permalink($trid);
        }

        return get_permalink($post->ID);
    }

    /**
     * Return the processed thumbnail.
     *
     * @since   3.0.1
     * @access  private
     * @param   int     $post_id
     * @return  string
     */
    private function get_thumbnail($post_id)
    {
        $thumbnail = '';

        if ( $this->options['thumbnail']['active'] ) {
            $trid = $this->translate->get_object_id($post_id, get_post_type($post_id));

            if ( $post_id != $trid ) {
                $post_id = $trid;
            }

            $thumbnail = $this->image->get(
                $post_id,
                [
                    $this->options['thumbnail']['width'],
                    $this->options['thumbnail']['height']
                ],
                $this->admin_options['tools']['thumbnail']['source'],
                $this->options['thumbnail']['crop'],
                $this->options['thumbnail']['build']
            );
        }

        return $thumbnail;
    }

    /**
     * Return the processed post/page title.
     *
     * @since   1.0.0
     * @access  private
     * @return  string
     */
    private function get_title()
    {
        global $post;

        $trid = $this->translate->get_object_id($post->ID, get_post_type($post->ID));

        if ( $post->ID != $trid ) {
            $title = get_the_title($trid);
        }
        else {
            $title = $post->post_title;
        }

        return apply_filters('the_title', $title, $post->ID);
    }

    /**
     * Return post views count.
     *
     * @since   2.0.0
     * @access  private
     * @return  int|float
     */
    private function get_pageviews()
    {
        global $post;

        $pageviews = 0;

        if ( $this->options['meta_tag']['views'] ) {

            // WordPress Popular Posts support
            if ( function_exists('wpp_get_views') ) {
                $trid = $this->translate->get_object_id(
                    $post->ID,
                    get_post_type($post->ID),
                    true,
                    $this->translate->get_default_language()
                );

                if ( $post->ID != $trid ) {
                    $pageviews = wpp_get_views($trid, NULL, false);
                }
                else {
                    $pageviews = wpp_get_views($post->ID, NULL, false);
                }
            }
            // WP-PostViews support
            elseif ( Helper::is_plugin_active('wp-postviews/wp-postviews.php') ) {
                // this plugin stores views data in the post meta table
                if ( ! $pageviews = get_post_meta($post->ID, 'views', true) ) {
                    $pageviews = 0;
                }
            }
            // Top-10 support
            elseif ( function_exists('get_tptn_post_count_only') ) {
                $blog_id = false;

                if ( is_multisite() ) {
                    $blog_id = get_current_blog_id();
                }

                $pageviews = get_tptn_post_count_only($post->ID, 'total', $blog_id);
            }

        }

        return apply_filters('recently_get_views', $pageviews, $post->ID);
    }

    /**
     * Return post comment count.
     *
     * @since   1.0.0
     * @access  private
     * @return  int
     */
    private function get_comments()
    {
        global $post;

        $comments = 0;

        if ( $this->options['meta_tag']['comment_count'] ) {
            $trid = $this->translate->get_object_id(
                $post->ID,
                get_post_type($post->ID),
                true,
                $this->translate->get_default_language()
            );

            if ( $post->ID != $trid ) {
                $comments = get_comments_number($trid);
            }
            else {
                $comments = get_comments_number($post->ID);
            }
        }

        return $comments;
    }

    /**
     * Get post date.
     *
     * @since   1.0.0
     * @access  private
     * @return  string
     */
    private function get_date()
    {
        global $post;

        $date = '';

        if ( $this->options['meta_tag']['date']['active'] ) {
            $date = ( 'relative' == $this->options['meta_tag']['date']['format'] ) 
                ? sprintf(__('%s ago', 'recently'), human_time_diff(strtotime($post->post_date), current_time('timestamp')))
                : date_i18n($this->options['meta_tag']['date']['format'], strtotime($post->post_date));
        }

        return $date;
    }

    /**
     * Get post taxonomies.
     *
     * @since   1.0.0
     * @access  private
     * @return  string
     */
    private function get_taxonomies()
    {
        global $post;

        $post_tax = '';

        if (
            (isset($this->options['meta_tag']['category']) && $this->options['meta_tag']['category']) 
            || $this->options['meta_tag']['taxonomy'] 
        ) {
            $taxonomy = 'category';

            if (
                $this->options['meta_tag']['taxonomy']['active']
                && ! empty($this->options['meta_tag']['taxonomy']['names'])
            ) {
                $taxonomy = $this->options['meta_tag']['taxonomy']['names'];
            }

            $trid = $this->translate->get_object_id($post->ID, get_post_type($post->ID));

            if ( $post->ID != $trid ) {
                $terms = wp_get_post_terms($trid, $taxonomy);
            }
            else {
                $terms = wp_get_post_terms($post->ID, $taxonomy);
            }

            if ( ! is_wp_error($terms) ) {
                // Usage: https://wordpress.stackexchange.com/a/46824
                if ( has_filter('recently_post_exclude_terms') ) {
                    $args = apply_filters('recently_post_exclude_terms', array());
                    $terms = wp_list_filter($terms, $args, 'NOT');
                }

                if (
                    is_array($terms) 
                    && ! empty($terms)
                ) {
                    foreach( $terms as $term ) {
                        $term_link = get_term_link($term);

                        if ( is_wp_error($term_link) )
                            continue;

                        $post_tax .= "<a href=\"{$term_link}\" class=\"{$term->taxonomy} {$term->slug} {$term->taxonomy}-{$term->term_id}\">{$term->name}</a>, ";
                    }
                }
            }

            if ( '' != $post_tax )
                $post_tax = rtrim($post_tax, ", ");

        }

        return $post_tax;
    }

    /**
     * Get post author.
     *
     * @since   1.0.0
     * @access  private
     * @return  string
     */
    private function get_author()
    {
        global $post;

        $author = ( $this->options['meta_tag']['author'] )
          ? get_the_author_meta('display_name', $post->post_author)
          : "";

        return $author;
    }

    /**
     * Return post excerpt.
     *
     * @since   1.0.0
     * @access  private
     * @return  string
     */
    private function get_excerpt()
    {
        global $post;

        $excerpt = '';

        if ( $this->options['post-excerpt']['active'] ) {
            $trid = $this->translate->get_object_id($post->ID, get_post_type($post->ID));

            if ( $post->ID != $trid ) {
                $the_post = get_post($trid);

                $excerpt = ( empty($the_post->post_excerpt) )
                  ? $the_post->post_content
                  : $the_post->post_excerpt;
            }
            else {
                $excerpt = ( empty($post->post_excerpt) )
                  ? $post->post_content
                  : $post->post_excerpt;
            }

            // remove caption tags
            $excerpt = preg_replace("/\[caption.*\[\/caption\]/", "", $excerpt);

            // remove Flash objects
            $excerpt = preg_replace("/<object[0-9 a-z_?*=\":\-\/\.#\,\\n\\r\\t]+/smi", "", $excerpt);

            // remove iframes
            $excerpt = preg_replace("/<iframe.*?\/iframe>/i", "", $excerpt);

            // remove WP shortcodes
            $excerpt = strip_shortcodes($excerpt);

            // remove style/script tags
            $excerpt = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $excerpt);

            // remove HTML tags if requested
            if ( $this->options['post-excerpt']['keep_format'] ) {
                $excerpt = strip_tags($excerpt, '<a><b><i><em><strong>');
            } else {
                $excerpt = strip_tags($excerpt);

                // remove URLs, too
                $excerpt = preg_replace('_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS', '', $excerpt);
            }

        }

        // Balance tags, if needed
        if ( '' !== $excerpt ) {
            $excerpt = Helper::truncate($excerpt, $this->options['post-excerpt']['length'], $this->options['post-excerpt']['words']);

            if ( $this->options['post-excerpt']['keep_format'] ){
                $excerpt = force_balance_tags($excerpt);
            }
        }

        return $excerpt;
    }

    /**
     * Return post rating.
     *
     * @since   1.0.0
     * @access  private
     * @return  string
     */
    private function get_rating()
    {
        global $post;

        $rating = '';

        if ( function_exists('the_ratings') && $this->options['rating'] ) {
            $rating = '<span class="recently-rating">' . the_ratings('span', $post->ID, false) . '</span>';
        }

        return $rating;
    }

    /**
     * Return post metadata.
     *
     * @since   2.0.0
     * @access  private
     * @param   string  $comments
     * @param   string  $pageviews
     * @param   string  $author
     * @param   string  $date
     * @param   string  $taxonomies
     * @return  array
     */
    private function get_metadata($comments, $pageviews, $author, $date, $taxonomies)
    {
        global $post;

        $meta = array();

        // STATS
        // comments
        if ( $this->options['meta_tag']['comment_count'] ) {
            /* translators: %s placeholder will be replaced with the comments count value */
            $comments_text = sprintf(_n('1 comment', '%s comments', $comments, 'recently'), number_format_i18n($comments));
            $meta[] = '<span class="recently-comments">' . $comments_text . '</span>';
        }

        // views
        if ( $this->options['meta_tag']['views'] ) {
            /* translators: %s placeholder will be replaced with the views count vaLue */
            $views_text = sprintf(_n('1 view', '%s views', $pageviews, 'recently'), number_format_i18n($pageviews));
            $meta[] = '<span class="recently-views">' . $views_text . "</span>";
        }

        // author
        if ( $this->options['meta_tag']['author'] ) {
            $display_name = '<a href="' . get_author_posts_url($post->post_author) . '">' . $author . '</a>';
            /* translators: %s placeholder will be replaced with the author name */
            $meta[] = '<span class="recently-author">' . sprintf(__('by %s', 'recently'), $display_name).'</span>';
        }

        // date
        if ( $this->options['meta_tag']['date']['active'] ) {
            /* translators: %s placeholder will be replaced with the post date */
            $meta[] = '<span class="recently-date">' . ( 'relative' == $this->options['meta_tag']['date']['format'] ? sprintf(__('posted %s', 'recently'), $date) : sprintf(__('posted on %s', 'recently'), $date) ) . '</span>';
        }

        // taxonomies
        if (
            $this->options['meta_tag']['taxonomy']['active']
            && $taxonomies
        ) {
            $meta[] = '<span class="recently-taxonomies">' . $taxonomies . '</span>';
        }

        return $meta;
    }

    /**
     * Parse content tags.
     *
     * @since   1.0.0
     * @access  private
     * @param   string  HTML string with content tags
     * @param	array   Post data
     * @param	bool    Used to display post rating (if functionality is available)
     * @return	string
     */
    private function format_content($string, $data, $rating = false)
    {
        if ( empty($string) || ( empty($data) || ! is_array($data) ) )
            return false;

        $params = array();
        $pattern = '/\{(pid|excerpt|summary|meta|stats|title|title_attr|image|thumb|thumb_img|thumb_url|rating|score|url|text_title|author|taxonomy|category|views|comments|date|total_items|item_position)\}/i';
        preg_match_all($pattern, $string, $matches);

        array_map('strtolower', $matches[0]);

        if ( in_array("{pid}", $matches[0]) ) {
            $string = str_replace("{pid}", $data['id'], $string);
        }

        if ( in_array("{title}", $matches[0]) ) {
            $string = str_replace("{title}", $data['title'], $string);
        }

        if ( in_array("{title_attr}", $matches[0]) ) {
            $string = str_replace("{title_attr}", $data['title_attr'], $string);
        }

        if ( in_array("{meta}", $matches[0]) || in_array("{stats}", $matches[0]) ) {
            $string = str_replace(array("{meta}", "{stats}"), $data['stats'], $string);
        }

        if ( in_array("{excerpt}", $matches[0]) || in_array("{summary}", $matches[0]) ) {
            $string = str_replace(array("{excerpt}", "{summary}"), $data['summary'], $string);
        }

        if ( in_array("{image}", $matches[0]) || in_array("{thumb}", $matches[0]) ) {
            $string = str_replace(array("{image}", "{thumb}"), $data['img'], $string);
        }

        if ( in_array("{thumb_img}", $matches[0]) ) {
            $string = str_replace("{thumb_img}", $data['img_no_link'], $string);
        }

        if ( in_array("{thumb_url}", $matches[0]) && ! empty($data['img_no_link']) ) {
            $dom = new \DOMDocument;

            if ( $dom->loadHTML($data['img_no_link']) ) {

                $img_tag = $dom->getElementsByTagName('img');

                if ( $img_tag->length ) {
                    foreach( $img_tag as $node ) {
                        if ( $node->hasAttribute('src') ) {
                            $string = str_replace("{thumb_url}", $node->getAttribute('src'), $string);
                        }
                    }
                }
            }
        }

        // WP-PostRatings check
        if ( $rating ) {
            if ( function_exists('the_ratings_results') && in_array("{rating}", $matches[0]) ) {
                $string = str_replace("{rating}", the_ratings_results($data['id']), $string);
            }

            if ( function_exists('expand_ratings_template') && in_array("{score}", $matches[0]) ) {
                $string = str_replace("{score}", expand_ratings_template('%RATINGS_SCORE%', $data['id']), $string);
                // removing the redundant plus sign
                $string = str_replace('+', '', $string);
            }
        }

        if ( in_array("{url}", $matches[0]) ) {
            $string = str_replace("{url}", $data['url'], $string);
        }

        if ( in_array("{text_title}", $matches[0]) ) {
            $string = str_replace("{text_title}", $data['text_title'], $string);
        }

        if ( in_array("{author}", $matches[0]) ) {
            $string = str_replace("{author}", $data['author'], $string);
        }

        if ( in_array("{taxonomy}", $matches[0]) || in_array("{category}", $matches[0]) ) {
            $string = str_replace(array("{taxonomy}", "{category}"), $data['taxonomy'], $string);
        }

        if ( in_array("{views}", $matches[0]) ) {
            $string = str_replace("{views}", $data['views'], $string);
        }

        if ( in_array("{comments}", $matches[0]) ) {
            $string = str_replace("{comments}", $data['comments'], $string);
        }

        if ( in_array("{date}", $matches[0]) ) {
            $string = str_replace("{date}", $data['date'], $string);
        }

        if ( in_array("{total_items}", $matches[0]) ) {
            $string = str_replace("{total_items}", $data['total_items'], $string);
        }

        if ( in_array("{item_position}", $matches[0]) ) {
            $string = str_replace("{item_position}", $data['item_position'], $string);
        }

        return apply_filters("recently_parse_custom_content_tags", $string, $data['id']);
    }

    /**
     * Sets public options.
     *
     * @since   3.0.0
     * @param   array
     */
    public function set_public_options(array $public_options = [])
    {
        $this->options = Helper::merge_array_r(
            $this->options,
            $public_options
        );
    }

    /**
     * Sets data.
     *
     * @since   3.0.0
     * @param   object
     */
    public function set_data($data)
    {
        $this->recents = $data;
    }

    /**
     * Output the HTML.
     *
     * @since   2.0.0
     */
    public function output()
    {
        echo $this->output;
    }

    /**
     * Return the HTML.
     *
     * @since   2.0.0
     * @return  string
     */
    public function get_output()
    {
        return $this->output;
    }
}
