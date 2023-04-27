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
     * Returns a new WP_Query object.
     *
     * @since   4.0.0
     * @param   array
     * @return  \WP_Query
     */
    protected function maybe_query(array $params)
    {
        // Get recent posts
        $this->query = $this->query_posts($params);

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
