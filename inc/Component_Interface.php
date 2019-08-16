<?php
/**
 * Sophia\Component_Interface interface
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia;

/**
 * Interface for a theme component.
 */
interface Component_Interface { // phpcs:ignore

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string;

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize();
}
