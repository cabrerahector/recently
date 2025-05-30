<?php
/**
 * Hooks into Autoptimize to exclude recently(.min).js 
 * from its JS optimization
 *
 * @package    Recently
 * @subpackage Recently/Compatibility
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Compatibility\Autoptimize;

use Recently\Compatibility\Compat;

class Autoptimize extends Compat
{
    /**
     * Registers filters to exclude recently(.min).jss from Autoptimize's JS optimization.
     *
     * @since  4.2.0
     */
    public function init()
    {
        if ( defined('AUTOPTIMIZE_PLUGIN_VERSION') ) {
            add_filter('autoptimize_filter_js_exclude', [$this, 'exclude_from_js_optimization']);
        }
    }

    /**
     * Adds recently(.min).js to the exclusions list.
     *
     * @param  array  An array of file exclusions
     * @return array  The modified array of exclusions
     */
    public function exclude_from_js_optimization($excluded) {

        $files = explode(', ', $excluded);
        $files[] = 'recently.min.js';
        $files[] = 'recently.js';
        $files = array_filter($files);

        $excluded = implode(', ', $files);

        return $excluded;
    }
}
