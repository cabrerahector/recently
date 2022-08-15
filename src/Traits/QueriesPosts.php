<?php
namespace Recently\Traits;

use Recently\Helper;

trait QueriesPosts
{
    /**
     * Query object.
     *
     * @since   4.0.0
     * @var     \WP_Query
     * @access  private
     */
    private $query;

    /**
     * Gets Query object from cache if it exists,
     * otherwise a new Query object will be
     * instantiated and returned.
     *
     * @since   4.0.0
     * @param   array
     * @return  \WP_Query
     */
    protected function maybe_query(array $params)
    {
        // Return cached results
        if ( $this->config['tools']['data']['cache']['active'] ) {

            $transient_name = md5(json_encode($params));
            $this->query = get_transient($transient_name);

            if ( false === $this->query ) {
                $this->query = $this->query_posts($params);

                switch( $this->config['tools']['data']['cache']['interval']['time'] ){
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

                $expiration = $time * $this->config['tools']['data']['cache']['interval']['value'];

                // Store transient
                set_transient($transient_name, $this->query, $expiration);

                // Store transient in Recently transients array for garbage collection
                $recently_transients = get_option('recently_transients');

                if ( ! $recently_transients ) {
                    $recently_transients = array($transient_name);
                    add_option('recently_transients', $recently_transients);
                } else {
                    if ( ! in_array($transient_name, $recently_transients) ) {
                        $recently_transients[] = $transient_name;
                        update_option('recently_transients', $recently_transients);
                    }
                }
            }

        } // Get recent posts
        else {
            $this->query = $this->query_posts($params);
        }

        return $this->query;
    }

    /**
     * Queries the database and returns the posts (if any met the criteria set by the user).
     *
     * @since   4.0.0
     * @global  object      $wpdb
     * @param   array       $instance
     * @return  null|array
     */
    protected function query_posts($params)
    {
        global $wpdb;

        // parse instance values
        $params = Helper::merge_array_r(
            $this->options,
            $params
        );

        $args = apply_filters('recently_pre_get_posts', $params['args'], $params['widget_id']);
        $args['lang'] = $this->translate->get_default_language();
        $args = apply_filters('recently_query_args', $args);

        return new \WP_Query($args);
    }
}
