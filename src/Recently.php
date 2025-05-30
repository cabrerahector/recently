<?php
/**
 * Plugin's main class.
 * 
 * Here everything gets initialized/loaded.
 */

namespace Recently;

use Recently\Admin\Admin;
use Recently\Block\Widget\Widget as BlockWidget;
use Recently\Compatibility\Compatibility;
use Recently\Front\Front;
use Recently\REST\Controller;
use Recently\Widget\Widget;

class Recently {

    /**
     * REST controller class.
     *
     * @since   3.0.0
     * @var     Rest\Controller $rest
     */
    private $rest;

    /**
     * Admin class.
     *
     * @since   3.0.0
     * @var     Admin\Admin $front
     */
    private $admin;

    /**
     * Front class.
     *
     * @since   3.0.0
     * @var     Front\Front $front
     */
    private $front;

    /**
     * Widget class.
     *
     * @since   3.0.0
     * @var     Widget\Widget $widget
     */
    private $widget;

    /**
     * Block Widget class.
     *
     * @since   4.0.0
     * @var     Block\Widget $widget
     * @access  private
     */
    private $block_widget;

    /**
     * Compatibility class.
     *
     * @var     Compatibility\Compatibility $compatibility
     * @access  private
     */
    private $compatibility;

    /**
     * Constructor.
     *
     * @since   3.0.0
     * @param   Rest\Controller             $rest
     * @param   Admin\Admin                 $admin
     * @param   Front\Front                 $front
     * @param   Widget\Widget               $widget
     * @param   Block\Widget                $block_widget
     * @param   Compatibility\Compatibility $compatibility
     */
    public function __construct(Controller $rest, Admin $admin, Front $front, Widget $widget, BlockWidget $block_widget, Compatibility $compatibility)
    {
        $this->rest = $rest;
        $this->admin = $admin;
        $this->front = $front;
        $this->widget = $widget;
        $this->block_widget = $block_widget;
        $this->compatibility = $compatibility;
    }

    /**
     * Initializes plugin.
     *
     * @since   3.0.0
     */
    public function init()
    {
        $this->compatibility->load();
        $this->rest->hooks();
        $this->admin->hooks();
        $this->front->hooks();
        $this->widget->hooks();
        $this->block_widget->hooks();
    }
}
