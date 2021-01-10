<?php
/**
 * Plugin bootstrap file.
 */
namespace Recently;

/** Composer autoloder */
require __DIR__ . '/../vendor/autoload.php';

register_activation_hook($recently_main_plugin_file, [__NAMESPACE__ . '\Activation\Activator', 'activate']);
register_deactivation_hook($recently_main_plugin_file, [__NAMESPACE__ . '\Activation\Deactivator', 'deactivate']);

$container = new Container\Container();
$container->configure([
    new Container\RecentlyConfiguration()
]);

$Recently = $container['recently'];
add_action('plugins_loaded', [$Recently, 'init']);
