<?php
/**
 * Contract to modify the DIC.
 */

namespace Recently\Container;

interface ContainerConfigurationInterface
{
    /**
     * Modifies the given dependency injection container.
     *
     * @since   3.0.0
     * @param   Container $container
     */
    public function modify(Container $container);
}