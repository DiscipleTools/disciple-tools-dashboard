<?php
/**
 * Home Apps Tile Template
 *
 * Displays Home Screen apps in a horizontally scrolling carousel.
 */

// Ensure DT_Home_Apps class is loaded (safety check)
if ( !class_exists( 'DT_Home_Apps' ) ) {
    $home_apps_file = get_template_directory() . '/dt-apps/dt-home/includes/class-home-apps.php';
    if ( file_exists( $home_apps_file ) ) {
        require_once $home_apps_file;
    }
}

// Get apps via filter hook
$apps = apply_filters( 'dt_home_screen_apps', [] );

// Ensure apps is an array
if ( !is_array( $apps ) ) {
    $apps = [];
}

// Only render if apps exist, otherwise hide the tile completely
if ( empty( $apps ) ) {
    // Hide the entire tile wrapper
    echo '<style>#dash-tile--' . esc_attr( $tile->handle ) . ' { display: none !important; }</style>';
    return;
}

// Get Home Screen app URL for "Show More" card
$app_key = 'apps_launcher_magic_key';
$app_user_key = get_user_option( $app_key );

// Construct URL if user has activated the Home Screen app
$show_more_url = false;
if ( $app_user_key && !empty( $app_user_key ) ) {
    // Try to get URL base from apps_list filter, fallback to hardcoded value
    $apps_list = apply_filters( 'dt_settings_apps_list', [] );
    $url_base = 'apps/launcher'; // Default fallback
    if ( isset( $apps_list[$app_key]['url_base'] ) ) {
        $url_base = $apps_list[$app_key]['url_base'];
    }
    $show_more_url = trailingslashit( trailingslashit( site_url() ) . $url_base ) . $app_user_key;
}
?>

<style>
/* Hide tile header and footer for Home Apps tile */
#dash-tile--<?php echo esc_attr( $tile->handle ); ?> .tile-header,
#dash-tile--<?php echo esc_attr( $tile->handle ); ?> .tile-footer {
    display: none !important;
}
</style>

<div class="tile-body tile-body--home-apps">
    <h2 class="home-apps-title"><?php echo esc_html( $tile->label ); ?></h2>
    <div id="home-apps-spinner-<?php echo esc_attr( $tile->handle ); ?>"
         style="display: inline-block"
         class="stats-spinner loading-spinner active">
    </div>
    <div class="home-apps-carousel-wrapper">
        <div class="home-apps-carousel"
             id="home-apps-carousel-<?php echo esc_attr( $tile->handle ); ?>"
             data-apps='<?php echo esc_attr( wp_json_encode( $apps ) ); ?>'
             data-show-more-url="<?php echo esc_attr( $show_more_url ? esc_url( $show_more_url ) : '' ); ?>">
            <!-- Apps will be rendered here via JavaScript -->
        </div>
    </div>
</div>

