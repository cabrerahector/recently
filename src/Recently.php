<?php
/**
 * Plugin's main class.
 * 
 * Here everything gets initialized/loaded.
 */

namespace Recently;

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
     * Constructor.
     *
     * @since   3.0.0
     * @param   I18N            $i18n
     * @param   Rest\Controller $rest
     * @param   Admin\Admin     $admin
     * @param   Front\Front     $front
     * @param   Widget\Widget   $widget
     */
    public function __construct(I18N $i18n, Rest\Controller $rest, Admin\Admin $admin, Front\Front $front, Widget\Widget $widget)
    {
        $this->i18n = $i18n;
        $this->rest = $rest;
        $this->admin = $admin;
        $this->front = $front;
        $this->widget = $widget;
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
    }
}
