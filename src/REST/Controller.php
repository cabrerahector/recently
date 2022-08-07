<?php
namespace Recently\REST;

use Recently\REST\WidgetEndpoint;

class Controller {

    /**
     * Taxonomies Endpoint.
     *
     * @since   4.0.0
     * @var     \WordPressPopularPosts\Rest\TaxonomiesEndpoint
     * @access  private
     */
    private $taxonomies_endpoint;

    /**
     * Themes Endpoint.
     *
     * @since   4.0.0
     * @var     \WordPressPopularPosts\Rest\ThemesEndpoint
     * @access  private
     */
    private $themes_endpoint;

    /**
     * Thumbnails Endpoint.
     *
     * @since   4.0.0
     * @var     \WordPressPopularPosts\Rest\ThumbnailsEndpoint
     * @access  private
     */
    private $thumbnails_endpoint;

    /**
     * Widget Endpoint.
     *
     * @var     \Recently\REST\WidgetEndpoint
     * @since   3.0.1
     * @access  private
     */
    private $widget_endpoint;

    /**
     * Initialize class.
     *
     * @since   3.0.0
     * @param   \Recently\REST\TaxonomiesEndpoint
     * @param   \Recently\REST\ThemesEndpoint
     * @param   \Recently\REST\ThumbnailsEndpoint
     * @param   \Recently\REST\WidgetEndpoint
     */
    public function __construct( TaxonomiesEndpoint $taxonomies_endpoint, ThemesEndpoint $themes_endpoint, ThumbnailsEndpoint $thumbnails_endpoint, WidgetEndpoint $widget_endpoint )
    {
        $this->taxonomies_endpoint = $taxonomies_endpoint;
        $this->themes_endpoint = $themes_endpoint;
        $this->thumbnails_endpoint = $thumbnails_endpoint;
        $this->widget_endpoint = $widget_endpoint;
    }

    /**
     * WordPress hooks.
     *
     * @since   3.0.0
     */
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registers REST endpoints.
     *
     * @since   3.0.0
     */
    public function register_routes()
    {
        $this->taxonomies_endpoint->register();
        $this->themes_endpoint->register();
        $this->thumbnails_endpoint->register();
        $this->widget_endpoint->register();
    }
}
