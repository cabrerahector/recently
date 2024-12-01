<?php
namespace Recently\Block\Widget;

use Recently\{ Helper, Image, Output, Themer, Translate };
use Recently\Block\Block;
use Recently\Traits\QueriesPosts;

class Widget extends Block
{
    use QueriesPosts;

    /**
     * Widget defaults.
     *
     * @since   4.0.0
     * @var     array
     */
    protected $options = [];

    /**
     * Administrative settings.
     *
     * @since   4.0.0
     * @var     array
     * @access  private
     */
    private $config = [];

    /**
     * Image object.
     *
     * @since   4.0.0
     * @var     Recently\Image
     * @access  private
     */
    private $thumbnail;

    /**
     * Output object.
     *
     * @since   4.0.0
     * @var     \Recently\Output
     * @access  private
     */
    private $output;

    /**
     * Translate object.
     *
     * @since   4.0.0
     * @var     \Recently\Translate    $translate
     * @access  private
     */
    private $translate;

    /**
     * Themer object.
     *
     * @since   4.0.0
     * @var     \Recently\Themer       $themer
     * @access  private
     */
    private $themer;

    /**
     * Default attributes.
     *
     * @since   4.0.0
     * @var     array      $defaults
     * @access  private
     */
    private $defaults;

    /**
     * Construct.
     *
     * @since   4.0.0
     * @param   array               $widget_options
     * @param   array               $config
     * @param   \Recently\Output    $output
     * @param   \Recently\Image     $image
     * @param   \Recently\Translate $translate
     * @param   \Recently\Themer    $themer
     */
    public function __construct(array $widget_options, array $config, Output $output, Image $thumbnail, Translate $translate, Themer $themer)
    {
        $this->options = $widget_options;
        $this->config = $config;
        $this->output = $output;
        $this->thumbnail = $thumbnail;
        $this->translate = $translate;
        $this->themer = $themer;

        $this->defaults = [
            'blockID' => '',
            'title' => '',
            'posts_per_page' => 10,
            'offset' => 0,
            'post_type' => 'post',
            'post_id' => '',
            'author_id' => '',
            'taxonomy' => 'category',
            'term_id' => '',
            'term_slug' => '',
            'shorten_title' => false,
            'title_length' => 0,
            'title_by_words' => 0,
            'display_post_excerpt' => false,
            'excerpt_length' => 0,
            'excerpt_format' => 0,
            'excerpt_by_words' => 0,
            'display_post_thumbnail' => false,
            'thumbnail_width' => 0,
            'thumbnail_height' => 0,
            'thumbnail_build' => 'manual',
            'thumbnail_size' => '',
            'rating' => false,
            'meta_comments' => true,
            'meta_views' => false,
            'meta_author' => false,
            'meta_date' => false,
            'meta_date_format' => 'F j, Y',
            'meta_taxonomy' => false,
            'meta_taxonomy_list' => [],
            'custom_html' => false,
            'header_start' => '<h2>',
            'header_end' => '</h2>',
            'recently_start' => '<ul class="recently-list">',
            'recently_end' => '</ul>',
            'post_html' => '<li>{thumb} {title} {meta}</li>',
            'theme' => ''
        ];
    }

    /**
     * Registers the block.
     *
     * @since   4.0.0
     */
    public function register()
    {
        // Block editor is not available, bail.
        if ( ! function_exists('register_block_type') ) {
            return;
        }

        wp_register_script(
            'block-recently-widget-js',
            plugin_dir_url(dirname(dirname(dirname(__FILE__)))) . 'assets/admin/js/blocks/block-recently-widget.js',
            ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor', 'wp-server-side-render'],
            filemtime(plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'assets/admin/js/blocks/block-recently-widget.js')
        );

        wp_localize_script(
            'block-recently-widget-js',
            '_recently',
            [
                'can_show_views' => ( function_exists('wpp_get_views') || Helper::is_plugin_active('wp-postviews/wp-postviews.php') || function_exists('get_tptn_post_count_only') ),
                'can_show_rating' => function_exists('the_ratings_results')
            ]
        );

        wp_register_style(
            'block-recently-editor-css',
            plugins_url('editor.css', __FILE__),
            [],
            filemtime(plugin_dir_path(__FILE__) . 'editor.css')
        );

        register_block_type(
            'recently/widget',
            [
                'editor_style'  => 'block-recently-editor-css',
                'editor_script' => 'block-recently-widget-js',
                'render_callback' => [$this, 'render'],
                'attributes' => [
                    '_editMode' => [
                        'type' => 'boolean',
                        'default' => true
                    ],
                    '_isSelected' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'blockID' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    'title' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    'posts_per_page' => [
                        'type' => 'number',
                        'default' => 10
                    ],
                    'offset' => [
                        'type' => 'number',
                        'default' => 0
                    ],
                    /* filters */
                    'post_type' => [
                        'type' => 'string',
                        'default' => 'post'
                    ],
                    'post_id' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    'author_id' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    'taxonomy' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    'term_id' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    'term_slug' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    /* post settings */
                    'shorten_title' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'title_length' => [
                        'type' => 'number',
                        'default' => 0
                    ],
                    'title_by_words' => [
                        'type' => 'number',
                        'default' => 0
                    ],
                    'display_post_excerpt' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'excerpt_format' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'excerpt_length' => [
                        'type' =>'number',
                        'default' => 0
                    ],
                    'excerpt_by_words' => [
                        'type' =>'number',
                        'default' => 0
                    ],
                    'display_post_thumbnail' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'thumbnail_width' => [
                        'type' =>'number',
                        'default' => 0
                    ],
                    'thumbnail_height' => [
                        'type' =>'number',
                        'default' => 0
                    ],
                    'thumbnail_build' => [
                        'type' => 'string',
                        'default' => 'manual'
                    ],
                    'thumbnail_size' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                    'rating' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    /* meta tag settings */
                    'meta_comments' => [
                        'type' => 'boolean',
                        'default' => true
                    ],
                    'meta_views' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'meta_author' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'meta_date' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'meta_date_format' => [
                        'type' => 'string',
                        'default' => 'F j, Y'
                    ],
                    'meta_taxonomy' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'meta_taxonomy_list' => [
                        'type' => 'array',
                        'default' => ['category']
                    ],
                    /* HTML markup settings */
                    'custom_html' => [
                        'type' => 'boolean',
                        'default' => false
                    ],
                    'header_start' => [
                        'type' => 'string',
                        'default' => '<h2>'
                    ],
                    'header_end' => [
                        'type' => 'string',
                        'default' => '</h2>'
                    ],
                    'recently_start' => [
                        'type' => 'string',
                        'default' => '<ul class="recently-list">'
                    ],
                    'recently_end' => [
                        'type' => 'string',
                        'default' => '</ul>'
                    ],
                    'post_html' => [
                        'type' => 'string',
                        'default' => '<li>{thumb} {title} {meta}</li>'
                    ],
                    'theme' => [
                        'type' => 'string',
                        'default' => ''
                    ],
                ]
            ]
        );
    }

    /**
     * Renders the block.
     *
     * @since   4.0.0
     * @param   array
     * @return  string
     */
    public function render(array $attributes)
    {
        extract($this->parse_attributes($attributes));

        $html = '<div class="recently' . (( isset($attributes['className']) && $attributes['className'] ) ? ' '. esc_attr($attributes['className']) : '') . '">';

        $theme_data = $this->themer->get_theme($theme);

        if ( ! isset($theme_data['json']) ) {
            $theme = '';
        }

        $this->options['args'] = [
            'post_type' => ! empty($post_type) ? explode(',', preg_replace('|[^a-z0-9,_-]|', '', $post_type)) : ['post'],
            'posts_per_page' => (int) $posts_per_page,
            'offset' => absint($offset)
        ];

        // Post IDs
        $post_id = rtrim( preg_replace('|[^0-9,]|', '', $post_id), ',' );

        if ( ! empty($post_id) ) {
            $this->options['args']['post__not_in'] = explode(',', $post_id);
        } else {
            if ( isset($this->options['args']['post__not_in']) )
                unset($this->options['args']['post__not_in']);
        }

        // Author IDs
        $author_id = trim( preg_replace('|[^0-9,-]|', '', $author_id), ',' );

        if ( ! empty($author_id) ) {
            $author_ids = explode(',', $author_id);
            $author_ids_in = [];
            $author_ids_out = [];

            for ( $i = 0; $i < count($author_ids); $i++ ) {
                if ( $author_ids[$i] >= 0 )
                    $author_ids_in[] = $author_ids[$i];
                else
                    $author_ids_out[] = $author_ids[$i] * -1;
            }

            if ( ! empty($author_ids_in) )
                $this->options['args']['author__in'] = $author_ids_in;

            if ( ! empty($author_ids_out) )
                $this->options['args']['author__not_in'] = $author_ids_out;

        } else {
            if ( isset($this->options['args']['author__in']) )
                unset($this->options['args']['author__in']);

            if ( isset($this->options['args']['author__not_in']) )
                unset($this->options['args']['author__not_in']);
        }

        // Taxonomy
        if (
            ! empty($taxonomy)
            && ! empty($term_id)
        ) {

            // Let's do some cleanup before attempting the filtering
            $taxonomy = trim($taxonomy, ' ;');
            $term_id = trim($term_id, ' ;');

            if (
                $taxonomy 
                && $term_id
            ) {

                $taxonomies = array_map('trim', explode(';', $taxonomy));
                $term_IDs_for_taxonomies = array_map('trim', explode(';', $term_id));

                // Apparently we have at least one taxonomy and matching term ID(s)
                if ( count($taxonomies) && count($term_IDs_for_taxonomies) ) {

                    // Parameters mismatch: we either have too little taxonomies
                    // or too little term ID groups, let's trim the excess
                    if ( count($taxonomies) != count($term_IDs_for_taxonomies) ) {
                        // We have more taxonomies than term ID groups,
                        // let's remove some taxonomies
                        if ( count($taxonomies) > count($term_IDs_for_taxonomies) ) {
                            $taxonomies = array_slice($taxonomies, 0, count($term_IDs_for_taxonomies));
                        }
                        // We have more term ID groups than taxonomies,
                        // let's remove some term ID groups
                        else {
                            $term_IDs_for_taxonomies = array_slice($term_IDs_for_taxonomies, 0, count($taxonomies));
                        }
                    }

                    $registered_taxonomies = get_taxonomies(['public' => true]);

                    foreach ( $taxonomies as $index => $taxonomy ) {
                        // Invalid taxonomy, discard the taxonomy
                        if ( ! isset($registered_taxonomies[$taxonomy]) ) {
                            unset($taxonomies[$index]);
                            unset($term_IDs_for_taxonomies[$index]);
                        }
                    }

                    // If we still have something we can use 
                    // for filtering, let's use it
                    if (
                        ! empty($taxonomies)
                        && ! empty($term_IDs_for_taxonomies)
                    ) {

                        $term_IDs = [];

                        foreach( $term_IDs_for_taxonomies as $term_IDs_for_single_taxonomy ) {
                            $term_IDs[] = explode(',', $term_IDs_for_single_taxonomy);
                        }

                        $in_term_IDs_for_taxonomies = [];
                        $out_term_IDs_for_taxonomies = [];

                        foreach( $term_IDs as $term_IDs_for_single_taxonomy ) {
                            $in_term_IDs_for_taxonomy = [];
                            $out_term_IDs_for_taxonomy = [];

                            foreach ( $term_IDs_for_single_taxonomy as $term_ID ) {
                                if ( $term_ID >= 0 )
                                    $in_term_IDs_for_taxonomy[] = trim($term_ID);
                                else
                                    $out_term_IDs_for_taxonomy[] = trim($term_ID) * -1;
                            }

                            $in_term_IDs_for_taxonomies[] = $in_term_IDs_for_taxonomy;
                            $out_term_IDs_for_taxonomies[] = $out_term_IDs_for_taxonomy;
                        }

                        if ( count($taxonomies) > 1 ) {
                            $this->options['args']['tax_query']['relation'] = 'AND';
                        }

                        foreach( $taxonomies as $taxIndex => $taxonomy ) {
                            $in_term_IDs = $in_term_IDs_for_taxonomies[$taxIndex];
                            $out_term_IDs = $out_term_IDs_for_taxonomies[$taxIndex];

                            if ( ! empty($in_term_IDs) ) {
                                $this->options['args']['tax_query'][] = [
                                    'taxonomy' => $taxonomy,
                                    'field' => 'term_id',
                                    'terms' => $in_term_IDs
                                ];
                            }

                            if ( ! empty($out_term_IDs) ) {
                                $this->options['args']['tax_query'][] = [
                                    'taxonomy' => $taxonomy,
                                    'field' => 'term_id',
                                    'terms' => $out_term_IDs,
                                    'operator' => 'NOT IN'
                                ];
                            }
                        }
                    }
                }
            }
        }
        else {
            unset($this->options['args']['tax_query']);
        }

        $this->options['title'] = apply_filters('widget_title', $title);

        $shorten_title = (bool) $shorten_title;
        $this->options['shorten_title'] = [
            'active' => $shorten_title,
            'length' => ( $shorten_title && Helper::is_number($title_length) && $title_length > 0 ) ? (int) $title_length : 0,
            'words' => ( $shorten_title && Helper::is_number($title_by_words) && $title_by_words > 0 ) ? (int) $title_by_words : 0
        ];

        $display_post_excerpt = (bool) $display_post_excerpt;
        $excerpt_format = (bool) $excerpt_format;
        $this->options['post-excerpt'] = [
            'active' => $display_post_excerpt,
            'length' => ( $display_post_excerpt && Helper::is_number($excerpt_length) ) ? (int) $excerpt_length : 0,
            'keep_format' => $excerpt_format,
            'words' => ( $display_post_excerpt && Helper::is_number($excerpt_by_words) && $excerpt_by_words > 0 ),
        ];

        $display_post_thumbnail = (bool) $display_post_thumbnail;
        $this->options['thumbnail'] = [
            'active' => ( 'predefined' == $thumbnail_build && $display_post_thumbnail ) ? true : ( Helper::is_number($thumbnail_width) && $thumbnail_width > 0 ),
            'width' => ( Helper::is_number($thumbnail_width) && $thumbnail_width > 0 ) ? (int) $thumbnail_width : 0,
            'height' => ( Helper::is_number($thumbnail_height) && $thumbnail_height > 0 ) ? (int) $thumbnail_height : 0,
            'build' => 'predefined' == $thumbnail_build ? 'predefined' : 'manual',
            'size' => empty($thumbnail_size) ? '' : $thumbnail_size,
        ];

        $this->options['rating'] = (bool) $rating;

        $this->options['meta_tag'] = [
            'comment_count' => (bool) $meta_comments,
            'views' => (bool) $meta_views,
            'author' => (bool) $meta_author,
            'date' => [
                'active' => (bool) $meta_date,
                'format' => empty($meta_date_format) ? 'F j, Y' : $meta_date_format
            ],
            'taxonomy' => [
                'active' => (bool) $meta_taxonomy,
                'names' => ( is_array($meta_taxonomy_list) && ! empty($meta_taxonomy_list) ) ? $meta_taxonomy_list : [],
            ]
        ];

        $this->options['markup'] = [
            'custom_html' => (bool) $custom_html,
            'title-start' => $header_start,
            'title-end' => $header_end,
            'recently-start' => $recently_start,
            'recently-end' => $recently_end,
            'post-html' => empty($post_html) ? '<li>{thumb} {title} {meta}</li>' : $post_html
        ];

        $this->options['theme'] = [
            'name' => $theme
        ];

        $this->options['widget_id'] = $blockID;

        // Has user set a title?
        if (
            ! empty($this->options['title'])
            && ! empty($this->options['markup']['title-start'])
            && ! empty($this->options['markup']['title-end'])
        ) {
            $html .= htmlspecialchars_decode($this->options['markup']['title-start'], ENT_QUOTES) . esc_html($this->options['title']) . htmlspecialchars_decode($this->options['markup']['title-end'], ENT_QUOTES);
        }

        $isAdmin = isset($_GET['isSelected']) ? $_GET['isSelected'] : false;

        if ( $this->config['tools']['data']['ajax'] && ! is_customize_preview() && ! $isAdmin ) {
            $html .= '<script type="application/json">' . json_encode($this->options) . '</script>';
            $html .= '<div class="recently-widget-block-placeholder"></div>';

            return $html . '</div>';
        }

        $recent_posts = $this->maybe_query($this->options['args']);

        $this->output->set_public_options($this->options);
        $this->output->set_data($recent_posts);
        $this->output->build_output();

        $html .= $this->output->get_output();
        $html .= '</div>';

        return $html;
    }

    /**
     * Parses attributes.
     *
     * @since   4.0.0
     * @param   array
     * @return  array
     */
    private function parse_attributes(array $atts = [])
    {
        $out = array();

        foreach ( $this->defaults as $name => $default ) {
            $out[$name] = array_key_exists($name, $atts) ? ( is_array($atts[$name]) ? $atts[$name] : trim($atts[$name]) ) : $default;
        }

        return $out;
    }
}