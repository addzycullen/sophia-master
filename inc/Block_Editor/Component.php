<?php
/**
 * Sophia\Block_Editor\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Block_Editor;

use Sophia\Component_Interface;
use kirki;
use function add_action;
use function add_filter;
use function add_theme_support;
use function get_theme_mod;
use function add_config;
use function add_panel;
use function add_section;
use function add_field;

/**
 * Class for integrating with the block editor.
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
        return 'block_editor';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'init', [ $this, 'actionRegisterEditorCustomiserSettings' ] );
        add_action( 'after_setup_theme', [ $this, 'actionAddBlockEditorSupport' ] );
        add_filter( 'kirki/styles_array', [ $this, 'outputColorClassesForBlockEditor' ] );
    }

    /**
     * Adds support for various editor features.
     */
    public function actionAddBlockEditorSupport()
    {
        // Add support for editor styles.
        add_theme_support( 'editor-styles' );

        // Add support for default block styles.
        add_theme_support( 'wp-block-styles' );

        // Add support for wide-aligned images.
        add_theme_support( 'align-wide' );

        /**
         * Add support for enqueuing the editor styles
         *
         * Use the add_editor_style function to enqueue and load CSS on the editor screen.
         * - Must use in conjunction with add_theme_support( 'editor-styles');
         * - Adds style-editor.css to the queue of stylesheets to be loaded in the editor.
         *
         * Uncomment to use.
         */
        // add_editor_style( 'style-editor.css' );

        /**
         * Add support for color palettes.
         *
         * To preserve color behavior across themes, use these naming conventions:
         * - Use primary and secondary color for main variations.
         * - Use `theme-[color-name]` naming standard for standard colors (red, blue, etc).
         * - Use `custom-[color-name]` for non-standard colors.
         *
         * Add the line below to disable the custom color picker in the editor.
         * add_theme_support( 'disable-custom-colors' );
         */

        $colors = get_theme_mod( 'sophia_color_palette_repeater' );
        $palette = [];
        foreach ( $colors as $color ) {
            $palette[] = [
                'name' => $color['sophia_color_palette_color_name'],
                'slug' => 'theme-' . generateSlugVar( $color['sophia_color_palette_color_name'] ),
                'color' => $color['sophia_color_palette_color_code'],
            ];
        }
        add_theme_support( 'editor-color-palette', $palette );

        /*
         * Add support custom font sizes.
         *
         * Add the line below to disable the custom color picker in the editor.
         * add_theme_support( 'disable-custom-font-sizes' );
         */
        add_theme_support(
            'editor-font-sizes',
            [
                [
                    'name'      => __( 'Small', 'sophia' ),
                    'shortName' => __( 'S', 'sophia' ),
                    'size'      => 16,
                    'slug'      => 'small',
                ],
                [
                    'name'      => __( 'Medium', 'sophia' ),
                    'shortName' => __( 'M', 'sophia' ),
                    'size'      => 25,
                    'slug'      => 'medium',
                ],
                [
                    'name'      => __( 'Large', 'sophia' ),
                    'shortName' => __( 'L', 'sophia' ),
                    'size'      => 31,
                    'slug'      => 'large',
                ],
                [
                    'name'      => __( 'Larger', 'sophia' ),
                    'shortName' => __( 'XL', 'sophia' ),
                    'size'      => 39,
                    'slug'      => 'larger',
                ],
            ]
        );
    }

    /**
     * Register Kirki Fields for Block Editor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function actionRegisterEditorCustomiserSettings()
    {
        // Add a base config for all fields
        Kirki::add_config(
            'base_setting',
            [
                'capability' => 'edit_theme_options',
                'option_type' => 'theme_mod',
            ]
        );

        // Add 'Editor Options' Panel to 'Global Styles' Panel
        Kirki::add_panel(
            'sophia_block_editor_options',
            [
                'title' => esc_attr__( 'Block Editor', 'sophia' ),
                'priority' => 10,
            ]
        );

        // Add 'Text Size Choices' Panel to 'Editor Options' Panel
        Kirki::add_section(
            'sophia_block_editor_font_sizes',
            [
                'title' => esc_attr__( 'Font Size Presets', 'sophia' ),
                'panel' => 'sophia_block_editor_options',
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type' => 'number',
                'settings' => 'sophia_block_editor_small_font_size',
                'label' => esc_attr__( 'Font Size: Small (px)', 'sophia' ),
                'section' => 'sophia_block_editor_font_sizes',
                'default' => 12,
                'choices' => [
                    'min' => 0,
                    'max' => 80,
                    'step' => 1,
                ],
                'output' => [
                    [
                        'element' => '.has-small-font-size',
                        'property' => 'font-size',
                        'suffix' => 'px !important',
                    ],
                ],
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type' => 'number',
                'settings' => 'sophia_block_editor_medium_font_size',
                'label' => esc_attr__( 'Font Size: Medium (px)', 'sophia' ),
                'section' => 'sophia_block_editor_font_sizes',
                'default' => 16,
                'choices' => [
                    'min' => 0,
                    'max' => 80,
                    'step' => 1,
                ],
                'output' => [
                    [
                        'element' => '.has-regular-font-size',
                        'property' => 'font-size',
                        'suffix' => 'px !important',
                    ],
                ],
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type' => 'number',
                'settings' => 'sophia_block_editor_large_font_size',
                'label' => esc_attr__( 'Font Size: Large (px)', 'sophia' ),
                'section' => 'sophia_block_editor_font_sizes',
                'default' => 22,
                'choices' => [
                    'min' => 0,
                    'max' => 80,
                    'step' => 1,
                ],
                'output' => [
                    [
                        'element' => '.has-large-font-size',
                        'property' => 'font-size',
                        'suffix' => 'px !important',
                    ],
                ],
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type' => 'number',
                'settings' => 'sophia_block_editor_extra_large_font_size',
                'label' => esc_attr__( 'Font Size: Extra Large (px)', 'sophia' ),
                'section' => 'sophia_block_editor_font_sizes',
                'default' => 36,
                'choices' => [
                    'min' => 0,
                    'max' => 80,
                    'step' => 1,
                ],
                'output' => [
                    [
                        'element' => '.has-larger-font-size',
                        'property' => 'font-size',
                        'suffix' => 'px !important',
                    ],
                ],
            ]
        );

        // Add 'Editor color Palette' Panel to 'Editor Options' Panel
        Kirki::add_section(
            'sophia_color_palette',
            [
                'title' => esc_attr__( 'Site Color Palette', 'sophia' ),
                'panel' => 'sophia_block_editor_options',
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type' => 'repeater',
                'label' => esc_attr__( 'Editor Color Palette', 'sophia' ),
                'section' => 'sophia_color_palette',
                'row_label' => [
                    'type' => 'text',
                    'value' => esc_attr__( 'Color', 'sophia' ),
                ],
                'button_label' => esc_attr__( 'Add a color to your palette', 'sophia' ),
                'settings' => 'sophia_color_palette_repeater',
                'fields' => [
                    'sophia_color_palette_color_name' => [
                        'type' => 'text',
                        'label' => esc_attr__( 'color Name', 'sophia' ),
                        'description' => esc_attr__( 'This will be added to the post editors color palette', 'sophia' ),
                        'default' => 'Orange',
                    ],
                    'sophia_color_palette_color_code' => [
                        'type' => 'color',
                        'label' => esc_attr__( 'color Value', 'sophia' ),
                        'description' => esc_attr__( 'This will be added to the post editors color palette', 'sophia' ),
                        'default' => '#FF6500',
                    ],
                ],
            ]
        );

    }

    /**
     * Add block editor color classes to the Frontend
     *
     * These classes are generated on elements in the block editor.
     * - Lets make them do something.
     *
     * @param array $css Array of Kirki css vars.
     *
     * @return array $css Modified of array of Kirki css vars.
     */
    public function outputColorClassesForBlockEditor( $css )
    {
        $colors = get_theme_mod( 'sophia_color_palette_repeater' );
        foreach ( $colors as $color ) {
            $cssTargetColor = '.has-theme-' . generateSlugVar( $color['sophia_color_palette_color_name'] ) . '-color';
            $cssTargetBackgroundColor = '.has-theme-' . generateSlugVar( $color['sophia_color_palette_color_name'] ) . '-background-color';

            $css['global'][ $cssTargetColor ]['color'] = $color['sophia_color_palette_color_code'];
            $css['global'][ $cssTargetBackgroundColor ]['background-color'] = $color['sophia_color_palette_color_code'];
        }
        return $css;
    }
}
