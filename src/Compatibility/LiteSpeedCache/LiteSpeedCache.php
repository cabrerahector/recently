<?php
/**
 * Hooks into LiteSpeedCache's hooks
 * to exclude recently(.min).js from its JS optimization
 *
 * @package    Recently
 * @subpackage Recently/Compatibility
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Compatibility\LiteSpeedCache;

use Recently\Compatibility\Compat;

class LiteSpeedCache extends Compat
{
    /**
     * Registers filters to exclude recently(.min).js from LSC's JS optimization.
     *
     * @since  4.2.0
     */
    public function init()
    {
        if ( defined('LSCWP_V') ) {
            add_filter('litespeed_optimize_js_excludes', [$this, 'exclude_from_js_optimization']);
            add_filter('litespeed_optm_js_defer_exc', [$this, 'exclude_from_js_optimization']);
            add_filter('litespeed_optm_js_delay_inc', [$this, 'exclude_from_js_optimization']);
        }
    }

    /**
     * Adds recently(.min).js to the exclusions list.
     *
     * @param  array  An array of file exclusions
     * @return array  The modified array of exclusions
     */
    public function exclude_from_js_optimization($excluded) {
        $excluded[] = 'recently.min.js';
        $excluded[] = 'recently.js';

        return $excluded;
    }
}
