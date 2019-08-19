<?php
/**
 * Sophia\Login_Screen\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Login_Screen;

use Sophia\Component_Interface;
use function Sophia\sophia;
use function add_action;
use function wp_enqueue_style;
use function get_template_directory_uri;
use function get_theme_file_uri;
use function get_theme_file_path;
use function home_url;
use function get_option;
use function DOMDocument;

/**
 * Class for customising Default Login Screen.
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
        return 'login_screen';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'login_enqueue_scripts', [ $this, 'sophiaEnqueueLoginCss' ], 10 );
        add_filter( 'login_headerurl', [ $this, 'sophiaChangeLoginLogoUrl' ] );
        add_filter( 'login_headertext', [ $this, 'sophiaChangeLoginLogoAltText' ] );

        add_action( 'login_head', [ $this, 'startPageOutputBuffering' ] );
        add_action( 'login_footer', [ $this, 'endPageOutputBuffering' ] );

        add_filter(
            'login_errors',
            function ( $a ) {
                return null;
            }
        );
    }

    /**
     * Enqueue login css
     */
    public function sophiaEnqueueLoginCss()
    {
        $css_uri = get_theme_file_uri( '/assets/dist/styles/' );
        $css_dir = get_theme_file_path( '/assets/dist/styles/' );

        $css_files = [
            'sophia-login-screen' => [
                'file'   => 'login.css',
            ],
        ];
        foreach ( $css_files as $handle => $data ) {
            $src     = $css_uri . $data['file'];
            $version = sophia()->getAssetVersion( $css_dir . $data['file'] );

            wp_enqueue_style( $handle, $src, [], $version, false );
        }
    }

    /**
     * Changing the logo link from wordpress.org to this site.
     */
    public function sophiaChangeLoginLogoUrl()
    {
        return home_url();
    }

    /**
     * Changing the alt text on the logo to show your site name
     */
    public function sophiaChangeLoginLogoAltText()
    {
        return get_option( 'blogname' );
    }

    /**
     * Alter Output of the page
     *
     * @param string $buffer Page content as a string.
     *
     * @return $buffer
     */
    public function callback( $buffer )
    {

        $buffer = sophia()->sanitizeOutput( $buffer );
        $buffer = preg_replace( '/<p>(.*?)<\/p>/', '<div class="form-field">$1</div>', $buffer );
        $buffer = preg_replace( '/<p class="(.*?)">(.*?)<\/p>/', '<div class="$1">$2</div>', $buffer );
        $buffer = str_replace( '<br />', '', $buffer );

        $buffer = preg_replace(
            '/<div class="form-field"><label for="(.*?)">(.*?)<input (.*?)><\/label><\/div>/',
            '<div class="form-field"><input $3 placeholder="$2" \/><label for="$1">$2<\/label></div>',
            $buffer
        );

        $dom = new \DOMDocument( '1.0', 'UTF-8' );
        $internalErrors = libxml_use_internal_errors( true );
        $dom->loadHTML( $buffer );
        $dom->preserveWhiteSpace = false; //phpcs:ignore
        $dom->loadHTML( $buffer );
        $dom->formatOutput = true; //phpcs:ignore
        libxml_use_internal_errors( $internalErrors );
        $buffer = $dom->saveXML( $dom->documentElement ); //phpcs:ignore

        return $buffer;
    }

    /**
     * Inititate Output buffering for the whole page.
     *
     * @return void
     */
    public function startPageOutputBuffering()
    {
        ob_start( array( 'self', 'callback' ) );
    }

    /**
     * End Output beffering for the whole page
     *
     * @return void
     */
    public function endPageOutputBuffering()
    {
        ob_end_flush();
    }


}
