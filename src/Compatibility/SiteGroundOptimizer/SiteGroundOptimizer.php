<?php
/**
 * Hooks into Speed Optimizer's (formerly known as SiteGround Optimizer) hook
 * to exclude recently(.min).js from its JS optimization
 *
 * @package    Recently
 * @subpackage Recently/Compatibility
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Compatibility\SiteGroundOptimizer;

use Recently\Compatibility\Compat;

class SiteGroundOptimizer extends Compat
{
    /**
     * Registers filters to exclude recently-js from SGO's JS optimization.
     */
    public function init()
    {
        if ( defined('SiteGround_Optimizer\VERSION') ) {
            add_filter('sgo_javascript_combine_exclude_ids', [$this, 'exclude_from_js_optimization']);
        }
    }

    /**
     * Adds recently-js to the exclusions list.
     *
     * @param  array  An array of file ID exclusions
     * @return array  The modified array of exclusions
     */
    public function exclude_from_js_optimization($excluded) {
        $excluded[] = 'recently-js';
        return $excluded;
    }
}
