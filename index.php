<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @since 1.0.0
 *
 * @package wp_rig
 */

namespace Sophia;

get_header();
?>
    <main id="primary" class="site-main">
        <?php
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
            }
        } else {
        }
        ?>
    </main><!-- #primary -->
<?php
get_sidebar();
get_footer();
