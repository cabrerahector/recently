<?php

class Recently_Helper {

    /**
     * Checks if a given plugin is active
     *
     * @since	1.0.0
     * @return	bool
     */
    public static function is_plugin_active( $plugin ) {

        if ( is_multisite() ) {
            $plugins = get_site_option( 'active_sitewide_plugins' );
            return isset( $plugins[$plugin] );
        }

        return in_array( $plugin, get_option( 'active_plugins' ) );

    }

    /**
     * Checks for valid number.
     *
     * @since   1.0.0
     * @param   int     number
     * @return  bool
     */
    public static function is_number( $number ){
        return !empty($number) && is_numeric($number) && (intval($number) == floatval($number));
    }

    /**
     * Returns server date.
     *
     * @since    2.1.6
     * @access   private
     * @return   string
     */
    public static function curdate() {
        return gmdate( 'Y-m-d', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ) );
    }

    /**
     * Returns mysql datetime.
     *
     * @since    2.1.6
     * @access   private
     * @return   string
     */
    public static function now() {
        return current_time( 'mysql' );
    }

    /**
     * Merges two associative arrays recursively.
     *
     * @since   1.0.0
     * @link    http://www.php.net/manual/en/function.array-merge-recursive.php#92195
     * @param   array   array1
     * @param   array   array2
     * @return  array
     */
    public static function merge_array_r( array $array1, array $array2 ) {

        $merged = $array1;

        foreach ( $array2 as $key => &$value ) {

            if ( is_array( $value ) && isset ( $merged[$key] ) && is_array( $merged[$key] ) ) {
                $merged[$key] = self::merge_array_r( $merged[$key], $value );
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;

    }

    /**
     * Debug function.
     *
     * @since   2.0.0
     * @param   mixed $v variable to display with var_dump()
     * @param   mixed $v,... unlimited optional number of variables to display with var_dump()
     */
    public static function debug( $v ) {

        if ( !defined('RECENTLY_DEBUG') || !RECENTLY_DEBUG )
            return;

        foreach ( func_get_args() as $arg ) {

            print "<pre>";
            var_dump($arg);
            print "</pre>";

        }

    }

    /**
     * Truncates text.
     *
     * @since   2.0.0
     * @param   string   $text
     * @param   int      $length
     * @param   bool     $truncate_by_words
     * @return  string
     */
    public static function truncate( $text = '', $length = 25, $truncate_by_words = false ) {

        if ( '' !== $text ) {

            // Truncate by words
            if ( $truncate_by_words ) {

                $words = explode( " ", $text, $length + 1 );

                if ( count($words) > $length ) {
                    array_pop( $words );
                    $text = rtrim( implode(" ", $words), ",." ) . " ...";
                }

            }
            // Truncate by characters
            elseif ( strlen($text) > $length ) {
                $text = rtrim( mb_substr($text, 0, $length , get_bloginfo('charset')), " ,." ) . "...";
            }

        }

        return $text;

    }

    /**
     * Gets post/page ID if current page is singular
     *
     * @since	1.0.0
     */
    public static function is_single() {

        if (
            is_singular() 
            && !is_front_page() 
            && !is_preview() 
            && !is_trackback() 
            && !is_feed() 
            && !is_robots() 
            && !is_customize_preview() 
        ) {
            return get_queried_object_id();
        }

        return false;

    }

} // End Recently_Helper class
