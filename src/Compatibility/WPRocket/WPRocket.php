<?php
/**
 * Hooks into WPRocket to exclude recently(.min).js 
 * from its JS optimizations
 *
 * @package    Recently
 * @subpackage Recently/Compatibility
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Compatibility\WPRocket;

use Recently\Compatibility\Compat;

class WPRocket extends Compat
{
    /**
     * Registers filters to exclude wpp(.min).jss from WPRocket's JS optimizations.
     *
     * @since  4.2.0
     */
    public function init()
    {
        if ( defined('WP_ROCKET_VERSION') ) {
            add_filter('rocket_exclude_js', [$this, 'exclude_from_js_optimization']);
            add_filter('rocket_exclude_defer_js', [$this, 'exclude_from_js_optimization']);
            add_filter('rocket_delay_js_exclusions', [$this, 'exclude_from_js_optimization']);
            add_filter('rocket_cdn_reject_files', [$this, 'exclude_from_js_optimization']);
        }
    }

    /**
     * Removes recently(.min).js and its inline script tags from the optimization list.
     *
     * @param  array  An array of files to optimize
     * @return array  The modified array of files to optimize
     */
    public function exclude_from_js_optimization($scripts) {
        if ( is_array($scripts) ) {
            $scripts[] = 'recently/assets/front/js/(.*).js';
            $scripts[] = 'recently-block-inline-js';
        }

        return $scripts;
    }
}
