<?php
/**
 * Hooks into W3TotalCache to exclude recently(.min).js 
 * from its JS optimization
 *
 * @package    Recently
 * @subpackage Recently/Compatibility
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

namespace Recently\Compatibility\W3TotalCache;

use Recently\Compatibility\Compat;

class W3TotalCache extends Compat
{
    /**
     * Registers filter to exclude recently(.min).jss from W3TotalCache's JS optimization.
     *
     * @since  4.2.0
     */
    public function init()
    {
        if ( defined('W3TC_VERSION') ) {
            add_filter('w3tc_minify_js_script_tags', [$this, 'exclude_from_js_optimization']);
        }
    }

    /**
     * Removes recently(.min).js from the minification list.
     *
     * @param  array  An array of files to minify
     * @return array  The modified array of files to minify
     */
    public function exclude_from_js_optimization($scripts) {

        if ( is_array($scripts) ) {
            $scripts = array_filter($scripts, function($script) {
                return (false === strpos($script, 'recently-js') && false === strpos($script, 'recently.js') && false === strpos($script, 'recently.min.js'));
            });
            // Reset keys
            $scripts = array_values($scripts);
        }

        return $scripts;
    }
}
