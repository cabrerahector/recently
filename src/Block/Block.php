<?php
/**
 * Contract to build blocks.
 */

namespace Recently\Block;

abstract class Block {

    /**
     * Registers action/filter hooks.
     *
     * @since  4.0.0
     */
    public function hooks()
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Registers the block.
     *
     * @since  4.0.0
     */
    abstract function register();

    /**
     * Renders the block.
     *
     * @since  4.0.0
     */
    abstract function render(array $attributes);
}
