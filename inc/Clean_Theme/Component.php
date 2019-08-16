<?php
/**
 * Sophia\Clean_Theme\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Clean_Theme;

use Sophia\Component_Interface;
use function add_action;
use function remove_action;
use function add_filter;
use function has_filter;
use function remove_filter;
use function is_admin;
use function wp_scripts;
use function unregister_taxonomy_for_object_type;
use function add_data;

/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `sophia()->getVersion()`
 * * `sophia()->getAssetVersion( string $filepath )`
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'clean_theme';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        // Tidy wp_head.
        add_action( 'init', [ $this, 'wpHeadCleanUp' ] );

        // Remove default 'Tags' taxonomy.
        add_action( 'init', [ $this, 'unregisterPostTags' ] );

        // clean up gallery output in wp.
        add_filter( 'gallery_style', [ $this, 'tidyGalleryStyle' ] );

        // Remove Comments from the frontend of SEO Indicator.
        add_filter( 'the_seo_framework_indicator', [ $this, '__return_false' ] );
    }

    /**
     * Cleanup Head
     *
     * WordPress head is a mess. Let's clean it up by removing all the junk we don't need.
     */
    public function wpHeadCleanUp()
    {
        // Remove category feeds.
        remove_action( 'wp_head', 'feed_links_extra', 3 );

        // Remove post and comment feeds.
        remove_action( 'wp_head', 'feed_links', 2 );

        // Remove EditURI link.
        remove_action( 'wp_head', 'rsd_link' );

        // Remove Windows live writer.
        remove_action( 'wp_head', 'wlwmanifest_link' );

        // Remove index link.
        remove_action( 'wp_head', 'index_rel_link' );

        // Remove previous link.
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );

        // Remove start link.
        remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );

        // Remove links for adjacent posts.
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

        // Remove WP version.
        remove_action( 'wp_head', 'wp_generator' );

        // Move jQuery to footer.
        if ( ! is_admin() ) {
            add_action( 'wp_head', [ $this, 'sendJqueryToFooter' ], 1, 0 );
        }

        // remove pesky injected css for recent comments widget.
        add_filter( 'wp_head', [ $this, 'orbitalRemoveWpWidgetRecentCommentsStyle' ], 1 );

        // clean up comment styles in the head.
        add_action( 'wp_head', [ $this, 'orbitalRemoveRecentCommentsStyle' ], 1 );
    }


    /**
     * Remove injected CSS for recent comments widget
     */
    public function orbitalRemoveWpWidgetRecentCommentsStyle()
    {
        if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
            remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
        }
    }

    /**
     * Remove injected CSS from recent comments widget
     */
    public function orbitalRemoveRecentCommentsStyle()
    {
        global $wp_widget_factory;
        if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
            remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
        }
    }

    /**
     * Remove injected CSS from gallery
     *
     * @param object $css      Styles to display.
     */
    public function tidyGalleryStyle( $css )
    {
        return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
    }

    /**
     * Get rid of tags on posts.
     *
     * @return void
     */
    public function unregisterPostTags()
    {

        unregister_taxonomy_for_object_type( 'post_tag', 'post' );
    }

    /**
     * Move jQuery Scripts to footer
     *
     * @return void
     */
    public function sendJqueryToFooter()
    {
        global $wp_scripts;
        $wp_scripts->add_data( 'jquery', 'group', 1 );
    }
}
