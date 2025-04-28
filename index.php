<?php
/**
 * Plugin Name:       Simple Alexa News Briefing
 * Plugin URI:        https://github.com/gioxx/wp-simplealexa
 * Description:       A modern, simple REST API–based WordPress plugin for Alexa flash briefing skills. Based on the original source code by Francesco Napoletano.
 * Version:           1.2.1
 * Author:            Gioxx
 * Author URI:        https://gioxx.org
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       simplealexa
 *
 * GitHub Plugin URI: https://github.com/gioxx/wp-simplealexa
 * GitHub Branch:     main
 * GitHub Languages:  true
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Rewrite rules for the REST API endpoint.
// This allows the plugin to be accessed via a pretty permalink structure.
add_action( 'init', function() {
    add_rewrite_rule(
        '^simplealexa/?$',
        'index.php?rest_route=/simplealexa/v1/briefing',
        'top'
    );
} );

// Plugin version.
define( 'SIMPLEALEXA_VERSION', '1.2.1' );

// Default number of items.
define( 'SIMPLEALEXA_DEFAULT_COUNT', 5 );

/**
 * Load plugin textdomain for translations.
 */
add_action( 'init', 'simplealexa_load_textdomain' );
function simplealexa_load_textdomain() {
    load_plugin_textdomain(
        'simplealexa',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}

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
    </div>
    <?php
}

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
