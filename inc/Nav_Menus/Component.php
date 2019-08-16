<?php
/**
 * Sophia\Nav_Menus\Component class
 *
 * @since 1.0.0
 *
 * @package sophia
 */

namespace Sophia\Nav_Menus;

use Sophia\Component_Interface;
use Sophia\Templating_Component_Interface;
use WP_Post;
use function add_action;
use function add_filter;
use function register_nav_menus;
use function esc_html__;
use function has_nav_menu;
use function wp_nav_menu;

/**
 * Class for managing navigation menus.
 *
 * Exposes template tags:
 * * `Sophia()->isPrimaryNavMenuActive()`
 * * `Sophia()->displayPrimaryNavMenu( array $args = [] )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

    const PRIMARY_NAV_MENU_SLUG = 'primary';

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'nav_menus';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'after_setup_theme', [ $this, 'actionRegisterNavMenus' ] );
        add_filter( 'walker_nav_menu_start_el', [ $this, 'filterPrimaryNavMenuDropdownSymbol' ], 10, 4 );
    }

    /**
     * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `sophia()`.
     *
     * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
     *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
     *               adding support for further arguments in the future.
     */
    public function templateTags() : array
    {
        return [
            'isPrimaryNavMenuActive' => [ $this, 'isPrimaryNavMenuActive' ],
            'displayPrimaryNavMenu'   => [ $this, 'displayPrimaryNavMenu' ],
        ];
    }

    /**
     * Registers the navigation menus.
     */
    public function actionRegisterNavMenus()
    {
        register_nav_menus(
            [
                static::PRIMARY_NAV_MENU_SLUG => esc_html__( 'Primary', 'sophia' ),
            ]
        );
    }

    /**
     * Adds a dropdown symbol to nav menu items with children.
     *
     * Adds the dropdown markup after the menu link element,
     * before the submenu.
     *
     * Javascript converts the symbol to a toggle button.
     *
     * @TODO:
     * - This doesn't work for the page menu because it
     *   doesn't have a similar filter. So the dropdown symbol
     *   is only being added for page menus if JS is enabled.
     *   Create a ticket to add to core?
     *
     * @param string  $item_output The menu item's starting HTML output.
     * @param WP_Post $item        Menu item data object.
     * @param int     $depth       Depth of menu item. Used for padding.
     * @param object  $args        An object of wp_nav_menu() arguments.
     * @return string Modified nav menu HTML.
     */
    public function filterPrimaryNavMenuDropdownSymbol( string $item_output, WP_Post $item, int $depth, $args ) : string
    {

        // Only for our primary menu location.
        if ( empty( $args->theme_location ) || static::PRIMARY_NAV_MENU_SLUG !== $args->theme_location ) {
            return $item_output;
        }

        // Add the dropdown for items that have children.
        if ( ! empty( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) ) {
            return $item_output . '<span class="dropdown"><i class="dropdown-symbol"></i></span>';
        }

        return $item_output;
    }

    /**
     * Checks whether the primary navigation menu is active.
     *
     * @return bool True if the primary navigation menu is active, false otherwise.
     */
    public function isPrimaryNavMenuActive() : bool
    {
        return (bool) has_nav_menu( static::PRIMARY_NAV_MENU_SLUG );
    }

    /**
     * Displays the primary navigation menu.
     *
     * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
     *                    arguments.
     */
    public function displayPrimaryNavMenu( array $args = [] )
    {
        if ( ! isset( $args['container'] ) ) {
            $args['container'] = 'ul';
        }

        $args['theme_location'] = static::PRIMARY_NAV_MENU_SLUG;

        wp_nav_menu( $args );
    }
}
