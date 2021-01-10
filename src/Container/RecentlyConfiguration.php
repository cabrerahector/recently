<?php
namespace Recently\Container;

use Recently\Settings;

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
            return new \Recently\I18N($container['admin_options']);
        });

        $container['translate'] = $container->service(function(Container $container) {
            return new \Recently\Translate();
        });

        $container['image'] = $container->service(function(Container $container) {
            return new \Recently\Image($container['admin_options']);
        });

        $container['output'] = $container->service(function(Container $container) {
            return new \Recently\Output($container['widget_options'], $container['admin_options'], $container['image'], $container['translate']);
        });

        $container['widget'] = $container->service(function(Container $container) {
            return new \Recently\Widget\Widget($container['widget_options'], $container['admin_options'], $container['output'], $container['image'], $container['translate']);
        });

        $container['rest'] = $container->service(function(Container $container) {
            return new \Recently\REST\Controller($container['admin_options'], $container['translate'], $container['output']);
        });

        $container['admin'] = $container->service(function(Container $container) {
            return new \Recently\Admin\Admin($container['admin_options'], $container['image']);
        });

        $container['front'] = $container->service(function(Container $container) {
            return new \Recently\Front\Front($container['admin_options'], $container['translate']);
        });

        $container['recently'] = $container->service(function(Container $container) {
            return new \Recently\Recently($container['rest'], $container['admin'], $container['front'], $container['widget']);
        });
    }
}
