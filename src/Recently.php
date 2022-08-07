<?php
/**
 * Plugin's main class.
 * 
 * Here everything gets initialized/loaded.
 */

namespace Recently;

use Recently\Admin\Admin;
use Recently\Block\Widget\Widget as BlockWidget;
use Recently\Front\Front;
use Recently\REST\Controller;
use Recently\Widget\Widget;

class Recently {

    /**
     * I18N class.
     *
     * @since   3.0.0
     * @var     I18N $i18n
     * @access  private
     */
    private $i18n;

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
     * Constructor.
     *
     * @since   3.0.0
     * @param   I18N            $i18n
     * @param   Rest\Controller $rest
     * @param   Admin\Admin     $admin
     * @param   Front\Front     $front
     * @param   Widget\Widget   $widget
     */
    public function __construct(I18N $i18n, Controller $rest, Admin $admin, Front $front, Widget $widget, BlockWidget $block_widget)
    {
        $this->i18n = $i18n;
        $this->rest = $rest;
        $this->admin = $admin;
        $this->front = $front;
        $this->widget = $widget;
        $this->block_widget = $block_widget;
    }

    /**
     * Initializes plugin.
     *
     * @since   3.0.0
     */
    public function init()
    {
        $this->i18n->load_plugin_textdomain();
        $this->rest->hooks();
        $this->admin->hooks();
        $this->front->hooks();
        $this->widget->hooks();
        $this->block_widget->hooks();
    }
}
