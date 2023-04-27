<?php
namespace Recently\REST;

use Recently\{ Translate, Output };
use Recently\Traits\QueriesPosts;

class WidgetEndpoint extends Endpoint
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
    protected $config = [];

    /**
     * Translate object.
     *
     * @since   3.0.1
     * @var     \Recently\Translate    $translate
     * @access  private
     */
    protected $translate;

    /**
     * Output object.
     *
     * @since   3.0.1
     * @var     \Recently\Output       $output
     * @access  private
     */
    protected $output;

    /**
     * Initializes class.
     *
     * @since   3.0.1
     * @param   array
     * @param   array
     * @param   \Recently\Translate
     * @param   \Recently\Output
     */
    public function __construct(array $widget_options, array $config, Translate $translate, Output $output)
    {
        $this->options = $widget_options;
        $this->config = $config;
        $this->translate = $translate;
        $this->output = $output;
    }

    /**
     * Register the routes for the objects of the controller.
     *
     * @since   3.0.1
     */
    public function register()
    {
        $version = '1';
        $namespace = 'recently/v' . $version;

        register_rest_route($namespace, '/widget/(?P<id>[\d]+)', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args'                => $this->get_widget_params(),
            )
        ));

        $version = '2';
        $namespace = 'recently/v' . $version;

        register_rest_route($namespace, '/widget/', [
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'get_widget_block'],
                'permission_callback' => '__return_true',
                'args'                => $this->get_widget_params(),
            ]
        ]);
    }

    /**
     * Get Recently widget instance.
     *
     * @since   3.0.1
     * @param   \WP_REST_Request    $request Full data about the request.
     * @return  \WP_Error|\WP_REST_Response
     */
    public function get_item($request)
    {
        $widget = get_option('widget_recently');

        if ( $data = $this->prepare_item_for_response($widget, $request) )
            return new \WP_REST_Response($data, 200);

        return new \WP_Error('invalid_instance', __('Invalid Widget Instance ID', 'recently'));
    }

    /**
     * Check if a given request has access to get items
     *
     * @since   3.0.1
     * @param   \WP_REST_Request    $request Full data about the request.
     * @return  \WP_Error|bool
     */
    public function get_items_permissions_check($request)
    {
        return true;
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @since   3.0.1
     * @param   \WP_REST_Request    $request Full data about the request.
     * @return  \WP_Error|bool
     */
    public function get_item_permissions_check($request)
    {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Prepare the item for the REST response
     *
     * @since   3.0.1
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response($widget, $request) {
        $instance_id = $request->get_param('id');
        $lang = $request->get_param('lang');

        if (
            ! $widget
            || ! $instance_id
            || ! isset($widget[$instance_id])
        ) {
            return false;
        }

        $instance = $widget[$instance_id];

        // Expose widget ID for customization
        if ( ! isset($instance['widget_id']) )
            $instance['widget_id'] = 'recently-' . $instance_id;

        $args = apply_filters('recently_pre_get_posts', $instance['args'], $instance['widget_id']);
        $args = apply_filters('recently_query_args', $args);

        $this->set_lang($lang, $args);

        $this->output->set_public_options($instance);
        $this->output->set_data(new \WP_Query($args));
        $this->output->build_output();

        return ['widget' => $this->output->get_output()];
    }

    /**
     * Retrieves a recent posts widget for display.
     *
     * @since 4.0.0
     *
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
     */
    public function get_widget_block($request)
    {
        $instance = $request->get_params();

        $is_single = $request->get_param('is_single');
        $lang = $request->get_param('lang');

        // Multilang support
        $this->set_lang($lang, $args);

        $recent_posts = $this->maybe_query($instance);

        if ( is_numeric($is_single) && $is_single > 0 ) {
            add_filter('recently_is_single', function($id) use ($is_single) {
                return $is_single;
            });
        }

        $this->output->set_public_options($instance);
        $this->output->set_data($recent_posts);
        $this->output->build_output();

        return [
            'widget' => $this->output->get_output()
        ];
    }

    /**
     * Retrieves the query params for getting a widget instance.
     *
     * @since 4.0.0
     *
     * @return array Query parameters for getting a widget instance.
     */
    public function get_widget_params()
    {
        return [
            'is_single' => [
                'type' => 'integer',
                'default' => null,
                'sanitize_callback' => 'absint'
            ],
            'lang' => [
                'type' => 'string',
                'default' => null,
                'sanitize_callback' => 'sanitize_text_field'
            ],
        ];
    }

}
