<?php
/**
 * Sophia\Acf_Extend\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Acf_Extend;

use Sophia\Component_Interface;
use acf;
use function add_action;
use function acf_render_field_setting;

/**
 * Class for Extending ACF
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
        return 'acf_extend';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'acf/render_field_settings/type=image', [ $this, 'sophiaAddDefaultImageValue' ] );
    }

    /**
     * Add default image setting to ACF image fields.
     *
     * Takes advantage of a field setting that already exists.
     *
     * @param array $field      Script Tags to display.
     */
    public function sophiaAddDefaultImageValue( $field )
    {
        acf_render_field_setting(
            $field,
            array(
                'label'        => 'Default Image',
                'instructions' => 'Used on new posts if no image is specified.',
                'type'         => 'image',
                'name'         => 'default_value',
            )
        );
    }
}
