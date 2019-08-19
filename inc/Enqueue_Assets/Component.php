<?php
/**
 * Sophia\Enqueue_Assets\Component class
 *
 * @package Sophia
 */

namespace Sophia\Enqueue_Assets;

use Sophia\Component_Interface;
use function Sophia\sophia;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function wp_enqueue_script;
use function wp_style_add_data;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_style_is;
use function post_password_required;
use function is_singular;
use function comments_open;
use function get_comments_number;
use function apply_filters;
use function add_query_arg;

/**
 * Class for managing scripts & stylesheets.
 */
class Component implements Component_Interface {

    /**
     * Associative array of CSS files, as $handle => $data pairs.
     * $data must be an array with keys 'file' (file path relative to 'assets/css' directory), and optionally 'global'
     * (whether the file should immediately be enqueued instead of just being registered) and 'preload_callback'
     * (callback function determining whether the file should be preloaded for the current request).
     *
     * Do not access this property directly, instead use the `getCssFiles()` method.
     *
     * @var array
     */
    protected $css_files;

    /**
     * Associative array of JS files, as $handle => $data pairs.
     * $data must be an array with keys 'file' (file path relative to 'assets/js' directory).
     *
     * Do not access this property directly, instead use the `getJsFiles()` method.
     *
     * @var array
     */
    protected $js_files;

    /**
     * Associative array of Google Fonts to load, as $font_name => $font_variants pairs.
     *
     * Do not access this property directly, instead use the `getGoogleFonts()` method.
     *
     * @var array
     */
    protected $google_fonts;

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'enqueue_assets';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'wp_enqueue_scripts', [ $this, 'actionEnqueueStyles' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'actionEnqueueScripts' ] );
        add_action( 'after_setup_theme', [ $this, 'actionAddEditorStyles' ] );
        add_filter( 'wp_resource_hints', [ $this, 'filterResourceHints' ], 10, 2 );
    }

    /**
     * Registers or enqueues scripts.
     *
     * Scripts that are global are enqueued.
     */
    public function actionEnqueueScripts()
    {
        $js_uri = get_theme_file_uri( '/assets/dist/scripts/' );
        $js_dir = get_theme_file_path( '/assets/dist/scripts/' );

        $js_files = $this->getJsFiles();
        foreach ( $js_files as $handle => $data ) {
            $src     = $js_uri . $data['file'];
            $version = sophia()->getAssetVersion( $js_dir . $data['file'] );
            $deps    = $data['deps'];
            $in_foot = $data['in_foot'];

            wp_enqueue_script( $handle, $src, $deps, $version, $in_foot );
        }
    }

    /**
     * Registers or enqueues stylesheets.
     *
     * Stylesheets that are global are enqueued.
     */
    public function actionEnqueueStyles()
    {
        // Enqueue Google Fonts.
        $google_fonts_url = $this->getGoogleFontsUrl();
        if ( ! empty( $google_fonts_url ) ) {
            wp_enqueue_style( 'sophia-fonts', $google_fonts_url, [], null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
        }

        $css_uri = get_theme_file_uri( '/assets/dist/styles/' );
        $css_dir = get_theme_file_path( '/assets/dist/styles/' );

        $css_files = $this->getCssFiles();
        foreach ( $css_files as $handle => $data ) {
            $src     = $css_uri . $data['file'];
            $version = sophia()->getAssetVersion( $css_dir . $data['file'] );

            wp_enqueue_style( $handle, $src, [], $version, $data['media'] );

            wp_style_add_data( $handle, 'precache', true );
        }
    }

    /**
     * Enqueues WordPress theme styles for the editor.
     */
    public function actionAddEditorStyles()
    {

        // Enqueue Google Fonts.
        $google_fonts_url = $this->getGoogleFontsUrl();
        if ( ! empty( $google_fonts_url ) ) {
            add_editor_style( $this->getGoogleFontsUrl() );
        }

        // Enqueue block editor stylesheet.
        add_editor_style( 'assets/css/editor/editor-styles.min.css' );
    }

    /**
     * Adds preconnect resource hint for Google Fonts.
     *
     * @param array  $urls          URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed.
     * @return array URLs to print for resource hints.
     */
    public function filterResourceHints( array $urls, string $relation_type ) : array
    {
        if ( 'preconnect' === $relation_type && wp_style_is( 'sophia-fonts', 'queue' ) ) {
            $urls[] = [
                'href' => 'https://fonts.gstatic.com',
                'crossorigin',
            ];
        }

        return $urls;
    }

    /**
     * Gets all JS files.
     *
     * @return array Associative array of $handle => $data pairs.
     */
    protected function getJsFiles() : array
    {
        if ( is_array( $this->js_files ) ) {
            return $this->js_files;
        }

        $js_files = [
            'sophia-global' =>
            [
                'file'    => 'base.js',
                'deps'    => [],
                'in_foot' => true,
            ],
        ];

        $this->js_files = [];
        foreach ( $js_files as $handle => $data ) {
            if ( is_string( $data ) ) {
                $data = [ 'file' => $data ];
            }

            if ( empty( $data['file'] ) ) {
                continue;
            }

            $this->js_files[ $handle ] = array_merge(
                [
                    'deps'    => [],
                    'in_foot' => true,
                ],
                $data
            );
        }

        return $this->js_files;
    }

    /**
     * Gets all CSS files.
     *
     * @return array Associative array of $handle => $data pairs.
     */
    protected function getCssFiles() : array
    {
        if ( is_array( $this->css_files ) ) {
            return $this->css_files;
        }

        $css_files = [
            'sophia-global'     => [
                'file'   => 'base.css',
                'global' => true,
            ],
            // 'sophia-comments'   => [
            // 'file'             => 'comments.css',
            // 'preload_callback' => function () {
            // return ! post_password_required() && is_singular() && ( comments_open() || get_comments_number() );
            // },
            // ],
        ];

        /**
         * Filters default CSS files.
         *
         * @param array $css_files Associative array of CSS files, as $handle => $data pairs.
         *                         $data must be an array with keys 'file' (file path relative to 'assets/css'
         *                         directory), and optionally 'global' (whether the file should immediately be
         *                         enqueued instead of just being registered) and 'preload_callback' (callback)
         *                         function determining whether the file should be preloaded for the current request).
         */
        $css_files = apply_filters( 'sophia_css_files', $css_files );

        $this->css_files = [];
        foreach ( $css_files as $handle => $data ) {
            if ( is_string( $data ) ) {
                $data = [ 'file' => $data ];
            }

            if ( empty( $data['file'] ) ) {
                continue;
            }

            $this->css_files[ $handle ] = array_merge(
                [
                    'global'           => false,
                    'media'            => 'all',
                ],
                $data
            );
        }

        return $this->css_files;
    }

    /**
     * Returns Google Fonts used in theme.
     *
     * @return array Associative array of $font_name => $font_variants pairs.
     */
    protected function getGoogleFonts() : array
    {
        if ( is_array( $this->google_fonts ) ) {
            return $this->google_fonts;
        }

        $google_fonts = [
            'IBM+Plex+Sans'  => [ '200', '300', '400', '700', '700' ],
            'IBM+Plex+Serif' => [ '500' ],
        ];

        /**
         * Filters default Google Fonts.
         *
         * @param array $google_fonts Associative array of $font_name => $font_variants pairs.
         */
        $this->google_fonts = (array) apply_filters( 'sophia_google_fonts', $google_fonts );

        return $this->google_fonts;
    }

    /**
     * Returns the Google Fonts URL to use for enqueuing Google Fonts CSS.
     *
     * Uses `latin` subset by default. To use other subsets, add a `subset` key to $query_args and the desired value.
     *
     * @return string Google Fonts URL, or empty string if no Google Fonts should be used.
     */
    protected function getGoogleFontsUrl() : string
    {
        $google_fonts = $this->getGoogleFonts();

        if ( empty( $google_fonts ) ) {
            return '';
        }

        $font_families = [];

        foreach ( $google_fonts as $font_name => $font_variants ) {
            if ( ! empty( $font_variants ) ) {
                if ( ! is_array( $font_variants ) ) {
                    $font_variants = explode( ',', str_replace( ' ', '', $font_variants ) );
                }

                $font_families[] = $font_name . ':' . implode( ',', $font_variants );
                continue;
            }

            $font_families[] = $font_name;
        }

        $query_args = [
            'family'  => implode( '|', $font_families ),
            'display' => 'swap',
        ];

        return add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }
}
