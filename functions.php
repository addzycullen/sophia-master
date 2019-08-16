<?php
/**
 * Theme functions file.
 *
 * This file is used to bootstrap the theme.
 *
 * @since 1.0.0
 *
 * @package   Sophia
 * @author    Adam Cullen <adam@orbital.co.uk>
 * @copyright 2019 Adam Cullen
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/addzycullen/sophia-master
 */

define( 'SOPHIA_VERSION', '1.0.0' );
define( 'SOPHIA_MINIMUM_WP_VERSION', '4.5' );
define( 'SOPHIA_MINIMUM_PHP_VERSION', '7.0' );


// Compatibility check - Bail if reqs aren't met.
if ( version_compare( $GLOBALS['wp_version'], SOPHIA_MINIMUM_WP_VERSION, '<' ) || version_compare( phpversion(), SOPHIA_MINIMUM_PHP_VERSION, '<' ) ) {
    require_once get_template_directory() . 'inc/back-compat.php';
    return;
}

// Include WordPress shims - support newer functionality.
require get_template_directory() . '/inc/wordpress-shims.php';

// Check if Kirki is loaded as a plugin, if not load the included version.
if ( ! class_exists( 'Kirki' ) ) {
    require_once get_template_directory() . '/inc/Kirki/kirki.php';
}// Make Kirki's 'Output' attribute write to a CSS file rather than inlin in the page

// Bootstrap the theme.
require_once get_template_directory() . '/inc/bootstrap.php';

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
