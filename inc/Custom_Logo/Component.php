<?php
/**
 * Sophia\Custom_Logo\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Custom_Logo;

use Sophia\Component_Interface;
use function add_action;
use function add_theme_support;
use function apply_filters;

/**
 * Class for adding custom logo support.
 *
 * @link https://codex.wordpress.org/Theme_Logo
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'custom_logo';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'after_setup_theme', [ $this, 'actionAddCustomLogoSupport' ] );
    }

    /**
     * Adds support for the Custom Logo feature.
     */
    public function actionAddCustomLogoSupport()
    {
        add_theme_support(
            'custom-logo',
            apply_filters(
                'sophia_custom_logo_args',
                [
                    'height'      => 250,
                    'width'       => 250,
                    'flex-width'  => false,
                    'flex-height' => false,
                ]
            )
        );
    }
}
