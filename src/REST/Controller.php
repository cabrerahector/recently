<?php
namespace Recently\REST;

use Recently\REST\WidgetEndpoint;

class Controller {

    /**
     * View Logger Endpoint.
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
     * @param   \Recently\REST\WidgetEndpoint
     */
    public function __construct( WidgetEndpoint $widget_endpoint )
    {
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
        $this->widget_endpoint->register();
    }
}
