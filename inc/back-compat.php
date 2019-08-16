<?php
/**
 * WP and PHP compatibility.
 *
 * This file was copied from the Mythic theme
 * and tweaked for use in the sophia theme.
 *
 * Functions used to gracefully fail when a theme doesn't meet the minimum WP or
 * PHP versions required. Note that only code that will work on PHP 5.2.4 should
 * go into this file. Otherwise, it'll break on sites not meeting the minimum
 *
 * @since 1.0.0
 *
 * @package   SOPHIA
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright 2019 Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://themehybrid.com/themes/mythic
 */

// Add actions to fail at certain points in the load process.
add_action( 'after_switch_theme', 'sophia_switch_theme' );
add_action( 'load-customize.php', 'sophia_load_customize' );
add_action( 'template_redirect', 'sophia_preview' );

/**
 * Returns the compatibility messaged based on whether the WP or PHP minimum
 * requirement wasn't met.
 *
 * @since  1.0.0
 * @return string
 */
function sophia_compat_message()
{
    if ( version_compare( $GLOBALS['wp_version'], '5.0', '<' ) ) {
        return sprintf(
            /* Translators: 1 is the required WordPress version and 2 is the user's current version. */
            esc_html__( 'Sophia requires at least WordPress version %1$s. You are running version %2$s. Please upgrade and try again.', 'sophia' ),
            '5.0',
            $GLOBALS['wp_version']
        );
    }

    if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
        return sprintf(
            /* Translators: 1 is the required PHP version and 2 is the user's current version. */
            esc_html__( 'Sophia requires at least PHP version %1$s. You are running version %2$s. Please upgrade and try again.', 'sophia' ),
            '5.6',
            PHP_VERSION
        );
    }

    return '';
}

/**
 * Switches to the previously active theme after the theme has been activated.
 *
 * @since  1.0.0
 * @param string $old_name Previous theme name/slug.
 * @return void
 */
function sophia_switch_theme( $old_name )
{

    switch_theme( $old_name ? $old_name : WP_DEFAULT_THEME );
    unset( $_GET['activated'] ); // phpcs:ignore WordPress.Security.NonceVerification
    add_action( 'admin_notices', 'sophia_upgrade_notice' );
}

/**
 * Outputs an admin notice with the compatibility issue.
 *
 * @since  1.0.0
 * @return void
 */
function sophia_upgrade_notice()
{

    printf( '<div class="error"><p>%s</p></div>', esc_html( sophia_compat_message() ) );
}

/**
 * Kills the loading of the customizer.
 *
 * @since  1.0.0
 * @return void
 */
function sophia_load_customize()
{
    wp_die( esc_html( sophia_compat_message() ), '', array( 'back_link' => true ) );
}

/**
 * Kills the customizer previewer on installs prior to WP 4.7.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function sophia_preview()
{
    if ( isset( $_GET['preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        wp_die( esc_html( sophia_compat_message() ) );
    }
}
/* Omit closing PHP tag to avoid "Headers already sent" issues. */
