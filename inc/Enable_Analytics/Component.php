<?php
/**
 * Sophia\Enable_Analytics\Component class
 *
 * @since 1.0.0
 *
 * @package Sophia
 */

namespace Sophia\Enable_Analytics;

use Sophia\Component_Interface;
use kirki;
use function add_action;
use function get_theme_mod;
use function add_section;
use function add_field;

/**
 * Class for integrating Google Analytics.
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
        return 'enable_analytics';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'init', [ $this, 'actionRegisterAnalyticsCustomiserSettings' ] );
        add_action( 'wp_head', [ $this, 'analyticsHeadSnippet' ], 0 );
        add_action( 'wp_body_open', [ $this, 'analyticsBodySnippet' ] );
    }

    /**
     * Register Kirki Fields for Analytics Fields
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function actionRegisterAnalyticsCustomiserSettings()
    {

        // Add 'Analytics Options' Section to 'Initial' Panel
        Kirki::add_section(
            'sophia_analytics_options',
            [
                'title' => esc_attr__( 'Analytics Options', 'sophia' ),
                'priority' => 100,
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type'        => 'select',
                'settings'    => 'sophia_analytics_type',
                'label'       => esc_html__( 'Analytics Type', 'sophia' ),
                'description' => esc_html__( 'Please choose what type of analytics you are using below, you will be presented with the appropiate fields once you have selected.', 'sophia' ),
                'section'     => 'sophia_analytics_options',
                'default'     => 'none',
                'priority'    => 10,
                'choices'     => [
                    'g_analytics'   => esc_html__( 'Google Analytics', 'sophia' ),
                    'g_tag_manager' => esc_html__( 'Google Tag Manager', 'sophia' ),
                    'none'  => esc_html__( 'No Analytics Installed', 'sophia' ),
                ],
            ]
        );

        Kirki::add_field(
            'base_setting',
            array(
                'type'        => 'text',
                'settings'    => 'sophia_analytics_g_tag_manager_id',
                'label'       => __( 'Google Tag Manager ID', 'sophia' ),
                'section'     => 'sophia_analytics_options',
                'description' => esc_attr__( 'Paste in your Tag Manager ID from your Google Tag Manager Dashboard.', 'sophia' ),
                'choices'     => [
                    'placeholder' => esc_attr__( 'GTM-XXXXXXX', 'sophia' ),
                ],
                'active_callback' => [
                    [
                        'setting'  => 'sophia_analytics_type',
                        'operator' => '==',
                        'value'    => 'g_tag_manager',
                    ],
                ],
            )
        );

        Kirki::add_field(
            'base_setting',
            array(
                'type'        => 'text',
                'settings'    => 'sophia_analytics_g_analytics_id',
                'label'       => __( 'Tracking ID', 'sophia' ),
                'section'     => 'sophia_analytics_options',
                'description' => esc_attr__( 'Paste in your Tracking ID from your Google Analytics Dashboard.', 'sophia' ),
                'choices'     => [
                    'placeholder' => esc_attr__( 'UA-XXXXXXX-XX', 'sophia' ),
                ],
                'active_callback' => [
                    [
                        'setting'  => 'sophia_analytics_type',
                        'operator' => '==',
                        'value'    => 'g_analytics',
                    ],
                ],
            )
        );
    }

    /**
     * Include the relevant analytics script in the head.
     *
     * See https://developers.google.com/analytics/devguides/collection/gajs/.
     */
    public function analyticsHeadSnippet()
    {
        $analytics_type = get_theme_mod( 'sophia_analytics_type' );
        if ( 'g_analytics' === $analytics_type ) {
            $g_analytics_id = get_theme_mod( 'sophia_analytics_g_analytics_id' );
            if ( ! empty( $g_analytics_id ) ) {
                ?>
                <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_html( $g_analytics_id ); ?>"></script> <?php // phpcs:ignore ?>
                <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '<?php echo esc_html( $g_analytics_id ); ?>');
                </script>
                <?php
            }
        } elseif ( 'g_tag_manager' === $analytics_type ) {
            $g_tag_id = get_theme_mod( 'sophia_analytics_g_tag_manager_id' );
            if ( ! empty( $g_tag_id ) ) {
                ?>
                <!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','<?php echo esc_html( $g_tag_id ); ?>');</script>
                <!-- End Google Tag Manager -->
                <?php
            }
        }
    }

    /**
     * Include the relevant analytics script after body tag.
     *
     * See https://developers.google.com/analytics/devguides/collection/gajs/.
     */
    public function analyticsBodySnippet()
    {
        $analytics_type = get_theme_mod( 'sophia_analytics_type' );
        if ( 'g_tag_manager' === $analytics_type ) {
            $g_tag_id = get_theme_mod( 'sophia_analytics_g_tag_manager_id' );
            if ( ! empty( $g_tag_id ) ) {
                ?>
                <!-- Google Tag Manager (noscript) -->
                <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_html( $g_tag_id ); ?>"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
                <!-- End Google Tag Manager (noscript) -->
                <?php
            }
        }
    }
}
