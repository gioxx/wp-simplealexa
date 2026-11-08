<?php
/**
 * Plugin Name:       Simple Alexa News Briefing
 * Plugin URI:        https://github.com/gioxx/wp-simplealexa
 * Description:       A modern, simple REST API–based WordPress plugin for Alexa flash briefing skills. Based on the original source code by Francesco Napoletano.
 * Version:           1.2.5
 * Author:            Gioxx
 * Author URI:        https://gioxx.org
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       simplealexa
 *
 * GitHub Plugin URI: gioxx/wp-simplealexa
 * GitHub Branch:     main
 * GitHub Languages:  true
 * Release Asset:     true
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin version.
define( 'SIMPLEALEXA_VERSION', '1.2.5' );

// Default number of items.
define( 'SIMPLEALEXA_DEFAULT_COUNT', 5 );

/**
 * Register REST API route for the flash briefing.
 */
add_action( 'rest_api_init', 'simplealexa_register_briefing_route' );
function simplealexa_register_briefing_route() {
    register_rest_route(
        'simplealexa/v1',
        '/briefing',
        [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'simplealexa_get_briefing_items',
            'permission_callback' => '__return_true',
        ]
    );
}

/**
 * Handler for briefing items.
 *
 * @param WP_REST_Request $request REST request object.
 * @return WP_REST_Response JSON response.
 */
function simplealexa_get_briefing_items( $request ) {
    // Get count from query or from settings
    $count_param   = $request->get_param( 'count' );
    $setting_count = absint( get_option( 'simplealexa_items_count', SIMPLEALEXA_DEFAULT_COUNT ) );
    $count         = $count_param ? absint( $count_param ) : $setting_count;

    $query = new WP_Query(
        [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => $count,
        ]
    );

    $items = [];
    foreach ( $query->posts as $post ) {
        $title   = get_the_title( $post );
        $excerpt = get_the_excerpt( $post );
        $text    = wp_strip_all_tags( $excerpt ? "$title, $excerpt" : $title );

        $items[] = [
            'uid'        => esc_url( get_permalink( $post ) ) . "-simplealexa-{$post->ID}",
            'updateDate' => get_date_from_gmt( $post->post_date_gmt, 'c' ),
            'titleText'  => wp_strip_all_tags( $title ),
            'mainText'   => $text,
        ];
    }

    return rest_ensure_response( $items );
}

/**
 * Add settings page under Settings.
 */
add_action( 'admin_menu', 'simplealexa_add_admin_menu' );
function simplealexa_add_admin_menu() {
    add_options_page(
        __( 'Alexa Briefing Settings', 'simplealexa' ),
        __( 'Alexa Briefing', 'simplealexa' ),
        'manage_options',
        'simplealexa',
        'simplealexa_settings_page'
    );
}

/**
 * Register plugin settings.
 */
add_action( 'admin_init', 'simplealexa_register_settings' );
function simplealexa_register_settings() {
    register_setting(
        'simplealexa_settings_group',
        'simplealexa_items_count',
        [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => SIMPLEALEXA_DEFAULT_COUNT,
        ]
    );

    add_settings_section(
        'simplealexa_main_section',
        __( 'Flash Briefing Options', 'simplealexa' ),
        '__return_false',
        'simplealexa'
    );

    add_settings_field(
        'simplealexa_items_count',
        __( 'Number of items', 'simplealexa' ),
        'simplealexa_items_count_field_callback',
        'simplealexa',
        'simplealexa_main_section'
    );
}

/**
 * Render items count field.
 */
function simplealexa_items_count_field_callback() {
    $count = get_option( 'simplealexa_items_count', SIMPLEALEXA_DEFAULT_COUNT );
    printf(
        '<input type="number" name="simplealexa_items_count" value="%d" min="1" class="small-text">',
        esc_attr( $count )
    );
    echo '<p class="description">' . esc_html__( 'Default number of briefing items if `count` param is not set.', 'simplealexa' ) . '</p>';
}

/**
 * Settings page output.
 */
function simplealexa_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Alexa Briefing Settings', 'simplealexa' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'simplealexa_settings_group' );
            do_settings_sections( 'simplealexa' );
            submit_button();
            ?>
        </form>
<div class="simplealexa-footer">
            <div class="simplealexa-footer__cards">
                <div class="simplealexa-footer__card simplealexa-footer__card--plugin">
                    <span class="simplealexa-footer__badge"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/images/plugin-icon.svg' ); ?>" width="20" height="20" alt=""></span>
                    <div class="simplealexa-footer__body">
                        <p class="simplealexa-footer__eyebrow">
                            <?php
                            /* translators: %s: plugin version number */
                            printf( esc_html__( 'Version %s', 'simplealexa' ), esc_html( SIMPLEALEXA_VERSION ) );
                            ?>
                        </p>
                        <h2 class="simplealexa-footer__title">Simple Alexa News Briefing</h2>
                        <p class="simplealexa-footer__text"><?php esc_html_e( 'Turn your latest posts into an Alexa Flash Briefing feed via a REST endpoint.', 'simplealexa' ); ?></p>
                        <p class="simplealexa-footer__text">
                            <?php
                            printf(
                                /* translators: 1: link to the author's site, 2: link to the plugin repository */
                                esc_html__( 'Made by %1$s. Source code on %2$s.', 'simplealexa' ),
                                '<a href="https://gioxx.org" target="_blank" rel="noopener noreferrer">Gioxx</a>',
                                '<a href="https://github.com/gioxx/wp-simplealexa" target="_blank" rel="noopener noreferrer">GitHub</a>'
                            );
                            ?>
                        </p>
                        <a class="button simplealexa-footer__action" href="https://github.com/gioxx/wp-simplealexa" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'View on GitHub', 'simplealexa' ); ?>
                            <span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'simplealexa' ); ?></span>
                        </a>
                    </div>
                </div>
                <div class="simplealexa-footer__card simplealexa-footer__card--gallery">
                    <img class="simplealexa-footer__logo" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/images/gioxx-plugins.svg' ); ?>" width="40" height="40" alt="">
                    <div class="simplealexa-footer__body">
                        <p class="simplealexa-footer__eyebrow">gioxx/WordPress</p>
                        <h2 class="simplealexa-footer__title"><?php esc_html_e( 'Discover more plugins by Gioxx', 'simplealexa' ); ?></h2>
                        <p class="simplealexa-footer__text"><?php esc_html_e( 'A curated collection of open-source plugins for security, analytics, performance and integrations.', 'simplealexa' ); ?></p>
                        <a class="button simplealexa-footer__action" href="https://wordpress.gioxx.org/" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'Browse the plugins', 'simplealexa' ); ?>
                            <span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'simplealexa' ); ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <p class="description simplealexa-trademark-notice">
                <?php esc_html_e( 'All trademarks mentioned are the property of their respective owners. Third-party trademarks, product names, trade names, corporate names and companies mentioned may be trademarks of their respective owners or registered trademarks of other companies and have been used for explanatory purposes only and for the benefit of the owner, without any intent to infringe existing copyright.', 'simplealexa' ); ?>
            </p>
        </div>
    </div>
    <?php
}


/**
 * Load the admin stylesheet on the plugin's settings page only.
 *
 * @param string $hook Admin page hook suffix.
 */
function simplealexa_enqueue_admin_assets( $hook ) {
    if ( 'settings_page_simplealexa' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'simplealexa-admin', plugin_dir_url( __FILE__ ) . 'assets/admin.css', array(), SIMPLEALEXA_VERSION );
}
add_action( 'admin_enqueue_scripts', 'simplealexa_enqueue_admin_assets' );

/**
 * Links under the plugin description on the Plugins screen: the GitHub
 * repository (unless the row already links to it) and the author's gallery.
 *
 * @param string[] $links Row meta links.
 * @param string   $file  Plugin basename of the row.
 * @return string[]
 */
function simplealexa_plugin_row_meta( $links, $file ) {
    if ( plugin_basename( __FILE__ ) !== $file ) {
        return $links;
    }
    if ( false === strpos( implode( ' ', (array) $links ), 'https://github.com/gioxx/wp-simplealexa' ) ) {
        $links[] = '<a href="https://github.com/gioxx/wp-simplealexa" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View on GitHub', 'simplealexa' ) . '</a>';
    }
    $links[] = '<a href="https://wordpress.gioxx.org/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'More plugins by Gioxx', 'simplealexa' ) . '</a>';

    return $links;
}
add_filter( 'plugin_row_meta', 'simplealexa_plugin_row_meta', 10, 2 );

/**
 * Activation hook: clear rewrite rules if needed.
 */
register_activation_hook( __FILE__, 'simplealexa_activation' );
function simplealexa_activation() {
    // Placeholder: future activation logic.
}

/**
 * Deactivation hook: cleanup.
 */
register_deactivation_hook( __FILE__, 'simplealexa_deactivation' );
function simplealexa_deactivation() {
    // Placeholder: future deactivation logic.
}
