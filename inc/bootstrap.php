<?php
/**
 * Bootstraps the Sophia theme.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

/**
 * Custom autoloader function for theme classes.
 *
 * @access private
 *
 * @param string $class_name Class name to load.
 * @return bool True if the class was loaded, false otherwise.
 */
function _sophia_autoload( $class_name )
{
    $namespace = 'Sophia';

    if ( strpos( $class_name, $namespace . '\\' ) !== 0 ) {
        return false;
    }

    $parts = explode( '\\', substr( $class_name, strlen( $namespace . '\\' ) ) );

    $path = get_template_directory() . '/inc';
    foreach ( $parts as $part ) {
        $path .= '/' . $part;
    }
    $path .= '.php';

    if ( ! file_exists( $path ) ) {
        return false;
    }

    require_once $path;

    return true;
}
spl_autoload_register( '_sophia_autoload' );

// Load the `sophia()` entry point function.
require get_template_directory() . '/inc/functions.php';

// Initialize the theme.
call_user_func( 'Sophia\sophia' );
