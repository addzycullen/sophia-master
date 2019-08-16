<?php
/**
 * Sophia\Custom_Background\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Custom_Background;

use Sophia\Component_Interface;
use function add_action;
use function add_theme_support;
use function apply_filters;

/**
 * Class for adding custom background support.
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'custom_background';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'after_setup_theme', [ $this, 'actionAddCustomBackgroundSupport' ] );
    }

    /**
     * Adds support for the Custom Background feature.
     */
    public function actionAddCustomBackgroundSupport()
    {
        add_theme_support(
            'custom-background',
            apply_filters(
                'sophia_custom_background_args',
                [
                    'default-color' => 'ffffff',
                    'default-image' => '',
                ]
            )
        );
    }
}
