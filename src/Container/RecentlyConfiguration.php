<?php
namespace Recently\Container;

use Recently\{ I18N, Image, Output, Recently, Settings, Themer, Translate };
use Recently\Admin\Admin;
use Recently\Front\Front;
use Recently\REST\{ Controller, WidgetEndpoint };
use Recently\Widget\Widget;

class RecentlyConfiguration implements ContainerConfigurationInterface
{
    /**
     * Modifies the given dependency injection container.
     *
     * @since   3.0.0
     * @param   Container $container
     */
    public function modify(Container $container)
    {
        $container['admin_options'] = Settings::get('admin_options');
        $container['widget_options'] = Settings::get('widget_options');

        $container['i18n'] = $container->service(function(Container $container) {
            return new I18N($container['admin_options']);
        });

        $container['translate'] = $container->service(function(Container $container) {
            return new Translate();
        });

        $container['image'] = $container->service(function(Container $container) {
            return new Image($container['admin_options']);
        });

        $container['themer'] = $container->service(function(Container $container) {
            return new Themer();
        });

        $container['output'] = $container->service(function(Container $container) {
            return new Output($container['widget_options'], $container['admin_options'], $container['image'], $container['translate'], $container['themer']);
        });

        $container['widget'] = $container->service(function(Container $container) {
            return new Widget($container['widget_options'], $container['admin_options'], $container['output'], $container['image'], $container['translate'], $container['themer']);
        });

        $container['widget_endpoint'] = $container->service(function(Container $container) {
            return new WidgetEndpoint($container['admin_options'], $container['translate'], $container['output']);
        });

        $container['rest'] = $container->service(function(Container $container) {
            return new Controller($container['widget_endpoint']);
        });

        $container['admin'] = $container->service(function(Container $container) {
            return new Admin($container['admin_options'], $container['image']);
        });

        $container['front'] = $container->service(function(Container $container) {
            return new Front($container['admin_options'], $container['translate']);
        });

        $container['recently'] = $container->service(function(Container $container) {
            return new Recently($container['i18n'], $container['rest'], $container['admin'], $container['front'], $container['widget']);
        });
    }
}
