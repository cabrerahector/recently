<?php
namespace Recently\Widget;

use Recently\{ Helper, Image, Output, Themer, Translate };
use Recently\Traits\QueriesPosts;

class Widget extends \WP_Widget {

    use QueriesPosts;

    /**
     * Plugin defaults.
     *
     * @since   1.0.0
     * @var     array
     */
    protected $options = array();

    /**
     * Administrative settings.
     *
     * @since   2.0.0
     * @var     array
     */
    private $admin_options = array();

    /**
     * Output object.
     *
     * @since   3.0.0
     * @var     \Recently\Output
     */
    private $output;

    /**
     * Image object.
     *
     * @since   3.0.0
     * @var     \Recently\Image
     */
    private $image;

    /**
     * Translate object.
     *
     * @since   3.0.0
     * @var     \Recently\Translate    $translate
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
     * Construct.
     *
     * @since   2.0.0
     * @param   array               $options
     * @param   array               $admin_options
     * @param   \Recently\Output    $output
     * @param   \Recently\Image     $image
     * @param   \Recently\Translate $translate
     * @param   \Recently\Themer    $themer
     */
    public function __construct(array $options, array $admin_options, Output $output, Image $image, Translate $translate, Themer $themer)
    {
        // Create the widget
        parent::__construct(
            'recently',
            'Recently',
            array(
                'classname' => 'recently',
                'description' => __('Your site\'s most recent posts.', 'recently'),
                'customize_selective_refresh' => false
            )
        );

        $this->options = $options;
        $this->admin_options = $admin_options;
        $this->output = $output;
        $this->image = $image;
        $this->translate = $translate;
        $this->themer = $themer;
    }

    /**
     * Widget hooks.
     *
     * @since   3.0.0
     */
    public function hooks()
    {
        // Register the widget
        add_action('widgets_init', [$this, 'register']);
        // Remove widget from Legacy Widget block
        add_filter('widget_types_to_hide_from_legacy_widget_block', [$this, 'remove_from_legacy_widget_block']);
    }

    /**
     * Registers the widget.
     *
     * @since   3.0.0
     */
    public function register()
    {
        register_widget($this);
    }

    /**
     * Outputs the content of the widget.
     *
     * @since   1.0.0
     * @param   array   args        The array of form elements
     * @param   array   instance    The current instance of the widget
     */
    public function widget($args, $instance)
    {
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
        extract($args, EXTR_SKIP);

        $instance = Helper::merge_array_r(
            \Recently\Settings::$defaults['widget_options'],
            (array) $instance
        );

        $markup = ( $instance['markup']['custom_html'] || has_filter('recently_custom_html') )
            ? 'custom'
            : 'regular';

        echo $before_widget . "\n";

        $title = ( '' != $instance['title'] ) ? apply_filters('widget_title', $instance['title']) : 'Recently';

        if (
            $instance['markup']['custom_html']
            && $instance['markup']['title-start'] != ""
            && $instance['markup']['title-end'] != ""
        ) {
            echo htmlspecialchars_decode($instance['markup']['title-start'], ENT_QUOTES) . $title . htmlspecialchars_decode($instance['markup']['title-end'], ENT_QUOTES);
        } else {
            echo $before_title . $title . $after_title;
        }

        if ( $this->admin_options['tools']['data']['ajax'] && ! is_customize_preview() ) {
            ?>
            <div class="recently-widget-placeholder" data-widget-id="<?php echo esc_attr($widget_id); ?>"></div>
            <?php
        } else {
            echo $this->get_recents($instance);
        }

        echo $after_widget . "\n";
    }

    /**
     * Generates the administration form for the widget.
     *
     * @since   1.0.0
     * @param   array   $instance    The array of keys and values for the widget.
     */
    public function form($instance)
    {
        $instance = Helper::merge_array_r(
            \Recently\Settings::$defaults['widget_options'],
            (array) $instance
        );

        include(plugin_dir_path(__FILE__) . '/form.php');
    }

    /**
     * Processes the widget's options to be saved.
     *
     * @since   1.0.0
     * @param   array   $new_instance   The previous instance of values before the update.
     * @param   array   $old_instance   The new instance of values to be generated via the update.
     * @return  array   $instance       Updated instance.
     */
    public function update($new_instance, $old_instance)
    {
        if ( empty($old_instance) ) {
            $old_instance = $this->options;
        } else {
            $old_instance = Helper::merge_array_r(
                $this->options,
                (array) $old_instance
            );
        }

        $instance = $old_instance;

        // Widget title
        $instance['title'] = apply_filters('widget_title', $new_instance['title']);

        /**
         * Query args
         */
        // Post type
        $instance['args']['post_type'] = ( '' == $new_instance['post_type'] )
            ? array('post')
            : explode(",", preg_replace('|[^a-z0-9,_-]|', '', $new_instance['post_type']));

        // Post per page
        $instance['args']['posts_per_page'] = ( Helper::is_number($new_instance['posts_per_page']) && $new_instance['posts_per_page'] > 0 )
            ? $new_instance['posts_per_page']
            : 10;

        // Offset
        $instance['args']['offset'] = ( Helper::is_number($new_instance['offset']) && $new_instance['offset'] >= 0 )
            ? $new_instance['offset']
            : 0;

        // OPTIONAL ARGS
        // Post IDs
        $new_instance['post_id'] = preg_replace('|[^0-9,]|', '', $new_instance['post_id']);

        if ( ! empty($new_instance['post_id']) ) {
            $instance['args']['post__not_in'] = explode(",", $new_instance['post_id']);
        } else {
            if ( isset($instance['args']['post__not_in']) )
                unset($instance['args']['post__not_in']);
        }

        // Taxonomy
        if (
            isset($new_instance['taxonomy'])
            && ! empty($new_instance['taxonomy'])
        ) {

            // Reset
            if ( isset($instance['args']['tax_query']) )
                unset($instance['args']['tax_query']);

            // Filter by Post Format
            if ( 'post_format' == $new_instance['taxonomy'] ) {

                $tax_slugs = trim(preg_replace('|[^a-z0-9,-]|', '', $new_instance['tax_slug']), ",");

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
                $new_instance['tax_id'] = trim(preg_replace('|[^0-9,-]|', '', $new_instance['tax_id']), ",");

                if ( '' != $new_instance['tax_id'] ) {
                    $tax_ids = explode(",", $new_instance['tax_id']);

                    for ( $i=0; $i < count($tax_ids); $i++ ) {
                        if ($tax_ids[$i] >= 0)
                            $tax_ids_in[] = $tax_ids[$i];
                        else
                            $tax_ids_out[] = $tax_ids[$i] * -1;
                    }
                }

                $instance['args']['tax_query']['relation'] = ( ! empty($tax_ids_in) && ! empty($tax_ids_out) ) ? 'AND' : 'OR';

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
            }
        }
        else {
            if ( isset($instance['args']['tax_query']) )
                unset($instance['args']['tax_query']);
        }

        // Author IDs
        $new_instance['author_id'] = trim(preg_replace('|[^0-9,-]|', '', $new_instance['author_id']), ",");

        if ( ! empty($new_instance['author_id']) ) {

            $author_ids = explode(",", $new_instance['author_id']);
            $author_ids_in = array();
            $author_ids_out = array();

            for ( $i=0; $i < count($author_ids); $i++ ) {
                if ($author_ids[$i] >= 0)
                    $author_ids_in[] = $author_ids[$i];
                else
                    $author_ids_out[] = $author_ids[$i];
            }

            if ( ! empty($author_ids_out) ) {
                // remove minus sign
                foreach ($author_ids_out as &$value)
                    $value *= (-1);
            }

            $instance['args']['author__in'] = $author_ids_in;
            $instance['args']['author__not_in'] = $author_ids_out;

        } else {
            if ( isset($instance['args']['author__in']) )
                unset($instance['args']['author__in']);

            if ( isset($instance['args']['author__not_in']) )
                unset($instance['args']['author__not_in']);
        }

        /**
         * Formatting options
         */
        // post title
        $instance['shorten_title']['active'] = isset($new_instance['shorten_title-active']);
        $instance['shorten_title']['words'] = $new_instance['shorten_title-words'];
        $instance['shorten_title']['length'] = ( Helper::is_number($new_instance['shorten_title-length']) && $new_instance['shorten_title-length'] > 0 )
            ? $new_instance['shorten_title-length']
            : 25;

        // post excerpt
        $instance['post-excerpt']['keep_format'] = isset($new_instance['post-excerpt-format']);
        $instance['post-excerpt']['words'] = $new_instance['post-excerpt-words'];
        $instance['post-excerpt']['active'] = isset($new_instance['post-excerpt-active']);
        $instance['post-excerpt']['length'] = ( Helper::is_number($new_instance['post-excerpt-length']) && $new_instance['post-excerpt-length'] > 0 )
            ? $new_instance['post-excerpt-length']
            : 55;

        // post thumbnail
        $instance['thumbnail']['active'] = false;
        $instance['thumbnail']['width'] = 75;
        $instance['thumbnail']['height'] = 75;

        $instance['thumbnail']['active'] = isset($new_instance['thumbnail-active']);
        $instance['thumbnail']['build'] = $new_instance['thumbnail-size-source'];

        // Use predefined thumbnail sizes
        if ( 'predefined' == $new_instance['thumbnail-size-source'] ) {

            $default_thumbnail_sizes = $this->image->get_sizes();
            $size = $default_thumbnail_sizes[ $new_instance['thumbnail-size'] ];

            $instance['thumbnail']['width'] = $size['width'];
            $instance['thumbnail']['height'] = $size['height'];
            $instance['thumbnail']['crop'] = $size['crop'];

        } // Set thumbnail size manually
        else {
            if ( Helper::is_number($new_instance['thumbnail-width']) && Helper::is_number($new_instance['thumbnail-height']) ) {
                $instance['thumbnail']['width'] = $new_instance['thumbnail-width'];
                $instance['thumbnail']['height'] = $new_instance['thumbnail-height'];
                $instance['thumbnail']['crop'] = true;
            }
        }

        // WP Post Rating support
        $instance['rating'] = isset($new_instance['rating']);

        // Statistics
        $instance['meta_tag']['comment_count'] = isset($new_instance['comment_count']);
        $instance['meta_tag']['views'] = isset($new_instance['views']);
        $instance['meta_tag']['author'] = isset($new_instance['author']);
        $instance['meta_tag']['date']['active'] = isset($new_instance['date']);
        $instance['meta_tag']['date']['format'] = empty($new_instance['date_format'])
            ? 'F j, Y'
            : $new_instance['date_format'];
        $instance['meta_tag']['taxonomy']['active'] = isset($new_instance['show_tax']);
        $instance['meta_tag']['taxonomy']['names'] = ( ! empty($new_instance['taxonomy_name']) && isset($new_instance['show_tax']) ) ? $new_instance['taxonomy_name'] : array();

        // Custom HTML
        $instance['markup']['custom_html'] = isset($new_instance['custom_html']);
        $instance['markup']['recently-start'] = empty($new_instance['recently-start'])
          ? ! $old_instance['markup']['custom_html'] && $instance['markup']['custom_html'] ? htmlspecialchars('<ul class="recently-list">', ENT_QUOTES) : ''
          : htmlspecialchars($new_instance['recently-start'], ENT_QUOTES);

        $instance['markup']['recently-end'] = empty($new_instance['recently-end'])
          ? ! $old_instance['markup']['custom_html'] && $instance['markup']['custom_html'] ? htmlspecialchars('</ul>', ENT_QUOTES) : ''
          : htmlspecialchars($new_instance['recently-end'], ENT_QUOTES);

        $instance['markup']['post-html'] = empty($new_instance['post-html'])
          ? htmlspecialchars('<li>{thumb} {title} {meta}</li>', ENT_QUOTES)
          : htmlspecialchars($new_instance['post-html'], ENT_QUOTES);

        $instance['markup']['title-start'] = empty($new_instance['title-start'])
          ? ! $old_instance['markup']['custom_html'] && $instance['markup']['custom_html'] ? '<h2>' : ''
          : htmlspecialchars($new_instance['title-start'], ENT_QUOTES);

        $instance['markup']['title-end'] = empty($new_instance['title-end'])
          ? ! $old_instance['markup']['custom_html'] && $instance['markup']['custom_html'] ? '</h2>' : '' :
          htmlspecialchars($new_instance['title-end'], ENT_QUOTES);

        $instance['theme'] = [
            'name' => isset($new_instance['theme']) ? $new_instance['theme'] : '',
            'applied' => isset($new_instance['theme']) ? (bool) $new_instance['theme-applied'] : false
        ];

        if ( ! isset($new_instance['theme']) || $old_instance['theme']['name'] != $new_instance['theme'] ) {
            $instance['theme']['applied'] = false;
        }

        $theme = $instance['theme']['name'] ? $this->themer->get_theme($instance['theme']['name']) : null;

        if (
            is_array($theme)
            && isset($theme['json'])
            && isset($theme['json']['config'])
            && is_array($theme['json']['config'])
            && ! $instance['theme']['applied']
        ) {
            $instance = Helper::merge_array_r(
                $instance,
                $theme['json']['config']
            );
            $instance['markup']['custom_html'] = true;
            $instance['theme']['applied'] = true;

            $current_sidebar_data = $this->get_sidebar_data();

            if ( $current_sidebar_data ) {
                $instance['markup']['title-start'] = htmlspecialchars($current_sidebar_data['before_title'], ENT_QUOTES);
                $instance['markup']['title-end'] = htmlspecialchars($current_sidebar_data['after_title'], ENT_QUOTES);
            }
        }

        return $instance;
    }

    /**
     * Outputs HTML list
     *
     * @since   1.0.0
     */
    public function get_recents($instance = null)
    {
        if ( is_array($instance) && ! empty($instance) ) {
            $instance['widget_id'] = $this->id;

            $recents = $this->maybe_query($instance);

            $this->output->set_public_options($instance);
            $this->output->set_data($recents);
            $this->output->build_output();

            $this->output->output();
        }
    }

    /**
     * Returns data on the current sidebar.
     *
     * @since   3.0.0
     * @access  private
     * @return  array|null
     */
    private function get_sidebar_data()
    {
        global $wp_registered_sidebars;
        $sidebars = wp_get_sidebars_widgets();

        foreach ( (array) $sidebars as $sidebar_id => $sidebar ) {
            if ( in_array($this->id, (array) $sidebar, true ) )
                return $wp_registered_sidebars[$sidebar_id];
        }

        return null;
    }

    /**
     * Removes the standard widget from the Legacy Widget block.
     *
     * @since   4.0.0
     * @param   array
     * @return  array
     */
    public function remove_from_legacy_widget_block(array $widget_types)
    {
        $widget_types[] = 'recently';
        return $widget_types;
    }
}
