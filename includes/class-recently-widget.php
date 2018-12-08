<?php

class Recently_Widget extends WP_Widget {

    /**
     * Plugin defaults.
     *
     * @since	1.0.0
     * @var		array
     */
    protected $options = array();

    /**
     * Administrative settings.
     *
     * @since	2.0.0
     * @var		array
     */
    private $admin_options = array();

    public function __construct(){

        // Create the widget
        parent::__construct(
            'recently',
            'Recently',
            array(
                'classname'		=>	'recently',
                'description'	=>	__( 'Your site\'s most recent posts.', 'recently' ),
                'customize_selective_refresh' => false
            )
        );

        $this->options = Recently_Settings::get( 'widget_options' );
        $this->admin_options = Recently_Settings::get( 'admin_options' );

        // Widget's AJAX hook
        if ( $this->admin_options['tools']['data']['ajax'] ) {
            add_action( 'wp_ajax_get_recently', array( $this, 'get_recents') );
            add_action( 'wp_ajax_nopriv_get_recently', array( $this, 'get_recents') );
        }

    }

    /**
     * Outputs the content of the widget.
     *
     * @since	1.0.0
     * @param	array	args		The array of form elements
     * @param	array	instance	The current instance of the widget
     */
    public function widget( $args, $instance ){
        /**
         * @var String $name
         * @var String $id
         * @var String $description
         * @var String $class
         * @var String $before_widget
         * @var String $after_widget
         * @var String $before_title
         * @var String $after_title
         * @var String $widget_id
         * @var String $widget_name
         */
        extract( $args, EXTR_SKIP );

        $instance = Recently_Helper::merge_array_r(
            Recently_Settings::$defaults[ 'widget_options' ],
            (array) $instance
        );

        $markup = ( $instance['markup']['custom_html'] || has_filter('recently_custom_html') )
            ? 'custom'
            : 'regular';

        echo "\n". "<!-- Recently widget v" . RECENTLY_VER . " [{$markup}] -->" . "\n";

        echo $before_widget . "\n";

        $title = ( '' != $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : 'Recently';

        if (
            $instance['markup']['custom_html']
            && $instance['markup']['title-start'] != ""
            && $instance['markup']['title-end'] != ""
        ) {
            echo htmlspecialchars_decode( $instance['markup']['title-start'], ENT_QUOTES) . $title . htmlspecialchars_decode($instance['markup']['title-end'], ENT_QUOTES );
        } else {
            echo $before_title . $title . $after_title;
        }

        if ( $this->admin_options['tools']['data']['ajax'] && !is_customize_preview() ) {
            if ( empty($before_widget) || !preg_match('/id="[^"]*"/', $before_widget) ) {
            ?>
            <p><?php printf( __('Error: cannot ajaxify Recently on this theme. It\'s missing the <em>id</em> attribute on <em>before_widget</em> (see <a href="%s" target="_blank" rel="external nofollow">register_sidebar</a> for more)', $this->plugin_slug ), 'https://codex.wordpress.org/Function_Reference/register_sidebar' ); ?>.</p>
            <?php
            } else {
            ?>
            <script type="text/javascript">
                window.addEventListener('DOMContentLoaded', function() {
                    if ( 'undefined' != typeof RecentlyWidget ) {
                        RecentlyWidget.get(
                            '<?php echo admin_url('admin-ajax.php'); ?>',
                            'action=get_recently&recently_widget_id=<?php echo $this->number; ?>',
                            function( response ){
                                document.getElementById('<?php echo $widget_id; ?>').innerHTML += response;
                            }
                        );
                    }
                });
            </script>
            <?php
            }
        } else {
            echo $this->get_recents( $instance );
        }

        echo $after_widget . "\n";
        echo "<!-- End Recently Plugin v" . RECENTLY_VER . " -->" . "\n";

    }

    /**
     * Generates the administration form for the widget.
     *
     * @since	1.0.0
     * @param	array	instance	The array of keys and values for the widget.
     */
    public function form( $instance ){

        $instance = Recently_Helper::merge_array_r(
            Recently_Settings::$defaults[ 'widget_options' ],
            (array) $instance
        );

        $recently_image = Recently_Image::get_instance();

        include( plugin_dir_path( __FILE__ ) . '/widget-form.php' );

    }

    /**
     * Processes the widget's options to be saved.
     *
     * @since	1.0.0
     * @param	array	new_instance	The previous instance of values before the update.
     * @param	array	old_instance	The new instance of values to be generated via the update.
     * @return	array	instance		Updated instance.
     */
    public function update( $new_instance, $old_instance ){

        $instance = $old_instance;

        $recently_image = Recently_Image::get_instance();

        // Widget title
        $instance['title'] = apply_filters( 'widget_title', $new_instance['title'] );

        /*
         * Query args
         */
        // Post type
        $instance['args']['post_type'] = ( '' == $new_instance['post_type'] )
            ? array('post')
            : explode(",", preg_replace( '|[^a-z0-9,_-]|', '', $new_instance['post_type'] ));

        // Post per page
        $instance['args']['posts_per_page'] = ( Recently_Helper::is_number($new_instance['posts_per_page']) && $new_instance['posts_per_page'] > 0 )
            ? $new_instance['posts_per_page']
            : 10;

        // Offset
        $instance['args']['offset'] = ( Recently_Helper::is_number($new_instance['offset']) && $new_instance['offset'] >= 0 )
            ? $new_instance['offset']
            : 0;

        // OPTIONAL ARGS
        // Post IDs
        $new_instance['post_id'] = preg_replace( '|[^0-9,]|', '', $new_instance['post_id'] );

        if ( !empty($new_instance['post_id']) ) {
            $instance['args']['post__not_in'] = explode(",", $new_instance['post_id']);
        } else {
            if ( isset($instance['args']['post__not_in']) )
                unset( $instance['args']['post__not_in'] );
        }

        // Taxonomy
        if (
            isset($new_instance['taxonomy'])
            && !empty( $new_instance['taxonomy'] )
        ) {

            // Reset
            if ( isset($instance['args']['tax_query']) )
                unset( $instance['args']['tax_query'] );

            // Filter by Post Format
            if ( 'post_format' == $new_instance['taxonomy'] ) {

                $tax_slugs = trim( preg_replace('|[^a-z0-9,-]|', '', $new_instance['tax_slug']), "," );

                $instance['args']['tax_query'][] = array(
                    'taxonomy' => $new_instance['taxonomy'],
                    'field' => 'slug',
                    'terms' => explode(",", $tax_slugs)
                );

            } // Filter by taxonomy ID
            else {

                $tax_ids = null;
                $tax_ids_in = array();
                $tax_ids_out = array();

                // Taxonomy IDs
                $new_instance['tax_id'] = trim( preg_replace('|[^0-9,-]|', '', $new_instance['tax_id']), "," );

                if ( '' != $new_instance['tax_id'] ) {

                    $tax_ids = explode(",", $new_instance['tax_id']);

                    for ( $i=0; $i < count($tax_ids); $i++ ) {
                        if ($tax_ids[$i] >= 0)
                            $tax_ids_in[] = $tax_ids[$i];
                        else
                            $tax_ids_out[] = $tax_ids[$i] * -1;
                    }

                }

                $instance['args']['tax_query']['relation'] = ( !empty($tax_ids_in) && !empty($tax_ids_out) ) ? 'AND' : 'OR' ;

                $instance['args']['tax_query'][] = array(
                    'taxonomy' => $new_instance['taxonomy'],
                    'field' => 'term_id',
                    'terms' => $tax_ids_in
                );

                $instance['args']['tax_query'][] = array(
                    'taxonomy' => $new_instance['taxonomy'],
                    'field' => 'term_id',
                    'terms' => $tax_ids_out,
                    'operator' => 'NOT IN'
                );

                if ( !empty($tax_ids_in) && !empty($tax_ids_out) ) {
                    
                }
                else {
                    
                }

            }

        }
        else {
            if ( isset($instance['args']['tax_query']) )
                unset( $instance['args']['tax_query'] );
        }

        // Author IDs
        $new_instance['author_id'] = trim( preg_replace('|[^0-9,-]|', '', $new_instance['author_id']), "," );

        if ( !empty($new_instance['author_id']) ) {

            $author_ids = explode(",", $new_instance['author_id']);
            $author_ids_in = array();
            $author_ids_out = array();

            for ( $i=0; $i < count($author_ids); $i++ ) {
                if ($author_ids[$i] >= 0)
                    $author_ids_in[] = $author_ids[$i];
                else
                    $author_ids_out[] = $author_ids[$i];
            }

            if ( !empty($author_ids_out) ) {
                // remove minus sign
                foreach ($author_ids_out as &$value)
                    $value *= (-1);
            }

            $instance['args']['author__in'] = $author_ids_in;
            $instance['args']['author__not_in'] = $author_ids_out;

        } else {
            if ( isset($instance['args']['author__in']) )
                unset( $instance['args']['author__in'] );

            if ( isset($instance['args']['author__not_in']) )
                unset( $instance['args']['author__not_in'] );
        }

        /*
         * Formatting options
         */
        // post title
        $instance['shorten_title']['active'] = isset( $new_instance['shorten_title-active'] );
        $instance['shorten_title']['words'] = $new_instance['shorten_title-words'];
        $instance['shorten_title']['length'] = ( Recently_Helper::is_number($new_instance['shorten_title-length']) && $new_instance['shorten_title-length'] > 0 )
            ? $new_instance['shorten_title-length']
            : 25;

        // post excerpt
        $instance['post-excerpt']['keep_format'] = isset( $new_instance['post-excerpt-format'] );
        $instance['post-excerpt']['words'] = $new_instance['post-excerpt-words'];
        $instance['post-excerpt']['active'] = isset( $new_instance['post-excerpt-active'] );
        $instance['post-excerpt']['length'] = ( Recently_Helper::is_number($new_instance['post-excerpt-length']) && $new_instance['post-excerpt-length'] > 0 )
            ? $new_instance['post-excerpt-length']
            : 55;

        // post thumbnail
        $instance['thumbnail']['active'] = false;
        $instance['thumbnail']['width'] = 75;
        $instance['thumbnail']['height'] = 75;

        if ( $recently_image->can_create_thumbnails() ) {

            $instance['thumbnail']['active'] = isset( $new_instance['thumbnail-active'] );
            $instance['thumbnail']['build'] = $new_instance['thumbnail-size-source'];

            // Use predefined thumbnail sizes
            if ( 'predefined' == $new_instance['thumbnail-size-source'] ) {

                $default_thumbnail_sizes = $recently_image->get_image_sizes();
                $size = $default_thumbnail_sizes[ $new_instance['thumbnail-size'] ];

                $instance['thumbnail']['width'] = $size['width'];
                $instance['thumbnail']['height'] = $size['height'];
                $instance['thumbnail']['crop'] = $size['crop'];

            } // Set thumbnail size manually
            else {
                if (Recently_Helper::is_number($new_instance['thumbnail-width']) && Recently_Helper::is_number($new_instance['thumbnail-height'])) {
                    $instance['thumbnail']['width'] = $new_instance['thumbnail-width'];
                    $instance['thumbnail']['height'] = $new_instance['thumbnail-height'];
                    $instance['thumbnail']['crop'] = true;
                }
            }

        }

        // WP Post Rating support
        $instance['rating'] = isset( $new_instance['rating'] );

        // Statistics
        $instance['meta_tag']['comment_count'] = isset( $new_instance['comment_count'] );
        $instance['meta_tag']['views'] = isset( $new_instance['views'] );
        $instance['meta_tag']['author'] = isset( $new_instance['author'] );
        $instance['meta_tag']['date']['active'] = isset( $new_instance['date'] );
        $instance['meta_tag']['date']['format'] = empty($new_instance['date_format'])
            ? 'F j, Y'
            : $new_instance['date_format'];
        $instance['meta_tag']['taxonomy']['active'] = isset( $new_instance['show_tax'] );
        $instance['meta_tag']['taxonomy']['names'] = ( !empty($new_instance['taxonomy_name']) && isset( $new_instance['show_tax'] ) ) ? $new_instance['taxonomy_name'] : array();

        // Custom HTML
        $instance['markup']['custom_html'] = isset( $new_instance['custom_html'] );
        $instance['markup']['recently-start'] = ( !isset($new_instance['recently-start']) || empty($new_instance['recently-start']) )
            ? '<ul class="recently-list">'
            : htmlspecialchars_decode( $new_instance['recently-start'], ENT_QUOTES );

        $instance['markup']['recently-end'] = ( !isset($new_instance['recently-end']) || empty($new_instance['recently-end']) )
            ? '</ul>'
            : htmlspecialchars_decode( $new_instance['recently-end'], ENT_QUOTES );

        $instance['markup']['post-html'] = empty($new_instance['post-html'])
            ? '<li>{thumb} {title} {metadata}</li>'
            : htmlspecialchars_decode( $new_instance['post-html'], ENT_QUOTES );

        $instance['markup']['title-start'] = !isset($new_instance['title-start'])
            ? '<h2>'
            : htmlspecialchars_decode( $new_instance['title-start'], ENT_QUOTES );

        $instance['markup']['title-end'] = !isset($new_instance['title-end'])
            ? '</h2>'
            : htmlspecialchars_decode( $new_instance['title-end'], ENT_QUOTES );

        return $instance;

    }

    /**
     * Returns HTML list
     *
     * @since	1.0.0
     */
    public function get_recents( $instance = null ) {

        if ( defined('DOING_AJAX') && DOING_AJAX ) {

            if (
                isset( $_GET['recently_widget_id'] ) 
                && Recently_Helper::is_number( $_GET['recently_widget_id'] )
            ) {

                $id = $_GET['recently_widget_id'];
                $widget_instances = $this->get_settings();

                if ( isset( $widget_instances[$id] ) ) {
                    $instance = $widget_instances[$id];

                    if ( !isset( $instance['widget_id'] ) ) {
                        $instance['widget_id'] = $this->id;
                    }
                }

            }

        }

        if ( is_array( $instance ) && !empty( $instance ) ) {

            // Return cached results
            if ( $this->admin_options['tools']['data']['cache']['active'] ) {

                $transient_name = md5( json_encode($instance) );
                $recents = get_transient( $transient_name );

                if ( false === $recents ) {

                    $recents = $this->query_posts( $instance );

                    switch( $this->admin_options['tools']['data']['cache']['interval']['time'] ){

                        case 'minute':
                            $time = 60;
                        break;

                        case 'hour':
                            $time = 60 * 60;
                        break;

                        case 'day':
                            $time = 60 * 60 * 24;
                        break;

                        case 'week':
                            $time = 60 * 60 * 24 * 7;
                        break;

                        case 'month':
                            $time = 60 * 60 * 24 * 30;
                        break;

                        case 'year':
                            $time = 60 * 60 * 24 * 365;
                        break;

                        default:
                            $time = 60 * 60;
                        break;

                    }

                    $expiration = $time * $this->admin_options['tools']['data']['cache']['interval']['value'];

                    // Store transient
                    set_transient( $transient_name, $recents, $expiration );

                    // Store transient in Recently transients array for garbage collection
                    $recently_transients = get_option('recently_transients');

                    if ( !$recently_transients ) {
                        $recently_transients = array( $transient_name );
                        add_option( 'recently_transients', $recently_transients );
                    } else {
                        if ( !in_array($transient_name, $recently_transients) ) {
                            $recently_transients[] = $transient_name;
                            update_option( 'recently_transients', $recently_transients );
                        }
                    }

                }

            } // Get popular posts
            else {
                $recents = $this->query_posts( $instance );
            }

            $output = new Recently_Output( $recents, $instance );

            echo ( $this->admin_options['tools']['data']['cache']['active'] ? '<!-- cached -->' : '' );
            $output->output();

        }

        if (
            defined('DOING_AJAX') 
            && DOING_AJAX && !is_preview() 
            && !( is_singular() && isset( $_GET['fl_builder'] ) )
        ) {
            wp_die();
        }

    }

    /**
     * Queries the database and returns the posts (if any met the criteria set by the user).
     *
     * @since	1.0.0
     * @global	object 		$wpdb
     * @param	array		$instance	Widget instance
     * @return	null|array	Array of posts, or null if nothing was found
     */
    protected function query_posts( $instance ) {

        global $wpdb;

        // parse instance values
        $instance = Recently_Helper::merge_array_r(
            $this->options,
            $instance
        );

        $args = apply_filters( 'recently_pre_get_posts', $instance['widget_id'], $instance['args'] );
        $args = apply_filters( 'recently_query_args', $args );

        return new WP_Query( $args );

    } // end query_posts

} // end class Recently_Widget
