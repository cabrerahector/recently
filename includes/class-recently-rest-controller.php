<?php

class Recently_REST_Endpoints extends WP_REST_Controller {

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $version = '1';
        $namespace = 'recently/v' . $version;

        register_rest_route( $namespace, '/widget/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => array(
                    'lang' => array(
                        'type' => 'string',
                        'default' => null,
                        'sanitize_callback' => 'sanitize_text_field'
                    ),
                ),
            )
        ) );
    }

    /**
     * Get Recently widget instance.
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item( $request ) {
        $widget = get_option( 'widget_recently' );

        if ( $data = $this->prepare_item_for_response( $widget, $request ) )
            return new WP_REST_Response( $data, 200 );

        return new WP_Error( 'invalid_instance', __( 'Invalid Widget Instance ID', 'recently' ) );
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check( $request ) {
        return true;
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }

    /**
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response( $widget, $request ) {
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

        $args = apply_filters( 'recently_pre_get_posts', $instance['args'], $instance['widget_id'] );
        $args = apply_filters( 'recently_query_args', $args );

        if ( $lang ) {
            $current_locale = get_locale();
            $locale = null;

            // Polylang support
            if ( function_exists('PLL') ) {
                $lang_object = PLL()->model->get_language($lang);
                $locale = ( $lang_object && isset($lang_object->locale) ) ? $lang_object->locale : null;

                $args['lang'] = $lang;
            } else {
                // WPML support
                global $sitepress;

                if ( is_object($sitepress) && method_exists($sitepress, 'get_locale_from_language_code') ) {
                    do_action( 'wpml_switch_language', $lang );
                    $locale = $sitepress->get_locale_from_language_code( $lang );

                    $args['suppress_filters'] = false;
                }
            }

            // Reload locale if needed
            if ( $locale && $locale != $current_locale ) {
                unload_textdomain( 'recently' );
                load_textdomain( 'recently', WP_LANG_DIR . '/plugins/recently-' . $locale . '.mo' );
            }
        }

        $output = new Recently_Output( new WP_Query($args), $instance );

        return ['widget' => $output->get_output()];
    }
}
