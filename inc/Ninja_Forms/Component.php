<?php
/**
 * Sophia\Ninja_Forms\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Ninja_Forms;

use Sophia\Component_Interface;
use function add_action;
use function wp_dequeue_style;

/**
 * Class for W3C Fix and HTML minifier
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'ninja_forms';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'nf_display_enqueue_scripts', [ $this, 'sophiaRemoveNinjaFormsStyles' ] );
    }

    /**
     * Remove Ninja Forms Styles.
     *
     * @return void
     */
    public function sophiaRemoveNinjaFormsStyles()
    {
        wp_dequeue_style( 'nf-display' );
    }


}
