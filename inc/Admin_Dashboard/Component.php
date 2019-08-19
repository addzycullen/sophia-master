<?php
/**
 * Sophia\Admin_Dashboard\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Admin_Dashboard;

use Sophia\Component_Interface;
use function add_action;
use function add_filter;
use function remove_meta_box;

/**
 * Class for customising the Admin Dashboard.
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
        return 'admin_area';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'admin_menu', [ $this, 'sophiaDisableDefaultDashboardWidgets' ] );
        add_filter( 'admin_footer_text', [ $this, 'sophiaCustomAdminFooter' ] );

    }

    /**
     * DASHBOARD WIDGETS
     *
     * Disable default dashboard widgets
     */
    public function sophiaDisableDefaultDashboardWidgets()
    {
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );       // Right Now Widget.
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' ); // Comments Widget.
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );  // Incoming Links Widget.
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );         // Plugins Widget.

        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );     // Quick Press Widget.
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );   // Recent Drafts Widget.
        remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );         // Remove Primary Dachboard.
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );       // Remove Secondary Dachboard.
    }

    /**
     * Customise Admin Footer
     */
    public function sophiaCustomAdminFooter()
    {
        echo '<span id="footer-thankyou">Built with <span class="heart" style="color: red; margin-right: 5px; font-size: 16px;">&#9829;</span> using the <strong>Sophia</strong> framework by <a href="https://www.orbital.co.uk" class="orbital" style="font-weight: 900; color: #FF6600; font-style: initial; font-family: helvetica;" target="_blank">Orbital</a></span> in sunny Bournemouth.';
    }
}
