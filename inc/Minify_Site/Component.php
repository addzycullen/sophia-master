<?php
/**
 * Sophia\Minify_Site\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Minify_Site;

use Sophia\Component_Interface;
use function Sophia\sophia;
use function add_action;
use function is_admin;

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
        return 'minify_site';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'wp_loaded', [ $this, 'sophiaMinifySiteBufferStart' ] );
        add_action( 'shutdown', [ $this, 'sophiaMinifySiteBufferEnd' ] );
    }

    /**
     * Start Page Output.
     *
     * @return void
     */
    public function sophiaMinifySiteBufferStart()
    {
        ob_start( array( 'self', 'sophiaMinifySiteCallback' ) );
    }

    /**
     * Finish Page output.
     */
    public function sophiaMinifySiteBufferEnd()
    {
        ob_get_clean();
    }

    /**
     * Modify Page output
     *
     * W3C Fix and HTML minifier by Ivijan-Stefan Stipic <infinitumform@gmail.com>
     *
     * @param string $buffer Html to be modified.
     *
     * @return $buffer Minified HTML.
     */
    // phpcs:disable
    private function sophiaMinifySiteCallback( $buffer )
    {
        if ( ! is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            // Remove type from javascript and CSS
            $buffer = preg_replace( "%[ ]type=[\'\"]text\/(javascript|css)[\'\"]%", '', $buffer );
            // Add alt attribute to images where not exists
            $buffer = preg_replace( '%(<img(?!.*?alt=([\'"]).*?\2)[^>]*?)(/?>)%', '$1 alt="" $3', $buffer );
            $buffer = preg_replace( '%\s+alt="%', ' alt="', $buffer );
            // clear HEAD
            $buffer = preg_replace_callback(
                '/(?=<head(.*?)>)(.*?)(?<=<\/head>)/s',
                function ( $matches ) {
                    return preg_replace(
                        array(
                            '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', // delete HTML comments
                        /* Fix HTML */
                            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                            '/\>\s+\</',    // strip whitespaces between tags
                        ),
                        array(
                            '',
                            /* Fix HTML */
                            '>',  // strip whitespaces after tags, except space
                            '<',  // strip whitespaces before tags, except space
                            '><',   // strip whitespaces between tags
                        ),
                        $matches[2]
                    );
                },
                $buffer
            );
            // clear BODY
            $buffer = preg_replace_callback(
                '/(?=<body(.*?)>)(.*?)(?<=<\/body>)/s',
                function ( $matches ) {
                    return preg_replace(
                        array(
                            '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', // delete HTML comments
                        /* Fix HTML */
                            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                            '/\>\s+\</',    // strip whitespaces between tags
                        ),
                        array(
                            '', // delete HTML comments
                        /* Fix HTML */
                            '>',  // strip whitespaces after tags, except space
                            '<',  // strip whitespaces before tags, except space
                            '> <',   // strip whitespaces between tags
                        ),
                        $matches[2]
                    );
                },
                $buffer
            );
            $buffer = preg_replace_callback(
                '/(?=<script(.*?)>)(.*?)(?<=<\/script>)/s',
                function ( $matches ) {
                    return preg_replace(
                        array(
                            '@\/\*(.*?)\*\/@s', // delete JavaScript comments
                            '@((^|\t|\s|\r)\/{2,}.+?(\n|$))@s', // delete JavaScript comments
                            '@(\}(\n|\s+)else(\n|\s+)\{)@s', // fix "else" statemant
                            '@((\)\{)|(\)(\n|\s+)\{))@s', // fix brackets position
                        // '@(\}\)(\t+|\s+|\n+))@s', // fix closed functions
                            '@(\}(\n+|\t+|\s+)else\sif(\s+|)\()@s', // fix "else if"
                            '@(if|for|while|switch|function)\(@s', // fix "if, for, while, switch, function"
                            '@\s+(\={1,3}|\:)\s+@s', // fix " = and : "
                            '@\$\((.*?)\)@s', // fix $(  )
                            '@(if|while)\s\((.*?)\)\s\{@s', // fix "if|while ( ) {"
                            '@function\s\(\s+\)\s{@s', // fix "function ( ) {"
                            '@(\n{2,})@s', // fix multi new lines
                            '@([\r\n\s\t]+)(,)@s', // Fix comma
                            '@([\r\n\s\t]+)?([;,{}()]+)([\r\n\s\t]+)?@', // Put all inline
                        ),
                        array(
                            "\n", // delete JavaScript comments
                            "\n", // delete JavaScript comments
                            '}else{', // fix "else" statemant
                            '){', // fix brackets position
                        // "});\n", // fix closed functions
                            '}else if(', // fix "else if"
                            '$1(',  // fix "if, for, while, switch, function"
                            ' $1 ', // fix " = and : "
                            '$' . '($1)', // fix $(  )
                            '$1 ($2) {', // fix "if|while ( ) {"
                            'function(){', // fix "function ( ) {"
                            "\n", // fix multi new lines
                            ',', // fix comma
                            '$2', // Put all inline
                        ),
                        $matches[2]
                    );
                },
                $buffer
            );
            // Clear CSS
            $buffer = preg_replace_callback(
                '/(?=<style(.*?)>)(.*?)(?<=<\/style>)/s',
                function ( $matches ) {
                    return preg_replace(
                        array(
                            '/([.#]?)([a-zA-Z0-9,_-]|\)|\])([\s|\t|\n|\r]+)?{([\s|\t|\n|\r]+)(.*?)([\s|\t|\n|\r]+)}([\s|\t|\n|\r]+)/s', // Clear brackets and whitespaces
                            '/([0-9a-zA-Z]+)([;,])([\s|\t|\n|\r]+)?/s', // Let's fix ,;
                            '@([\r\n\s\t]+)?([;:,{}()]+)([\r\n\s\t]+)?@', // Put all inline
                        ),
                        array(
                            '$1$2{$5} ', // Clear brackets and whitespaces
                            '$1$2', // Let's fix ,;
                            '$2', // Put all inline
                        ),
                        $matches[2]
                    );
                },
                $buffer
            );

            // Clean between HEAD and BODY
            $buffer = preg_replace( "%</head>([\s\t\n\r]+)<body%", '</head><body', $buffer );
            // Clean between BODY and HTML
            $buffer = preg_replace( "%</body>([\s\t\n\r]+)</html>%", '</body></html>', $buffer );
            // Clean between HTML and HEAD
            $buffer = preg_replace( "%<html(.*?)>([\s\t\n\r]+)<head%", '<html$1><head', $buffer );
        }

        return $buffer;
    }
    // phpcs:enable
}
