<?php
/**
 * Shims for recent WordPress functions
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

if ( ! function_exists( 'write_log' ) ) {
     /**
      * Write to Debug Log
      *
      * Add to your functions.php file for use in a theme.
      * Remember to remove from live environment.
      *
      * @param object $log contents of log.
      * @return void
      */
    function write_log( $log )
    {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}

/**
 * Adds backwards compatibility for wp_body_open() introduced with WordPress 5.2
 */
if ( ! function_exists( 'wp_body_open' ) ) {
    /**
     * Run the wp_body_open action.
     *
     * @return void
     */
    function wp_body_open()
    {
        do_action( 'wp_body_open' );
    }
}

/**
 * Generate slug version of a $var
 *
 * @param string $string String passed theough to convert.
 * @param int    $wordLimit Limits length of URL if passed.
 *
 * @return $string SEO vfriendly version of var.
 */
function generateSlugVar( $string, $wordLimit = 0 )
{
    $separator = '-';

    if ( 0 != $wordLimit ) {
        $wordArr = explode( ' ', $string );
        $string  = implode( ' ', array_slice( $wordArr, 0, $wordLimit ) );
    }

    $quoteSeparator = preg_quote( $separator, '#' );

    $trans = array(
        '&.+?;'                      => '',
        '[^\w\d _-]'                 => '',
        '\s+'                        => $separator,
        '(' . $quoteSeparator . ')+' => $separator,
    );

    $string = strip_tags( $string );
    foreach ( $trans as $key => $val ) {
        $string = preg_replace( '#' . $key . '#i', $val, $string );
    }

    $string = strtolower( $string );

    return trim( trim( $string, $separator ) );
}
