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

// Early return if class still doesn't exist to prevent fatal error
if ( !class_exists( 'DT_Home_Apps' ) ) {
    // Hide tile and return early to prevent fatal error
    echo '<style>#dash-tile--' . esc_attr( $tile->handle ) . ' { display: none !important; }</style>';
    return;
}

// Get apps via direct method call
$apps = DT_Home_Apps::instance()->get_apps_for_frontend( 'app' );

// Ensure apps is an array
if ( !is_array( $apps ) ) {
    $apps = [];
}

// Only render if apps exist, otherwise hide the tile completely
if ( empty( $apps ) ) {
    // Hide the entire tile wrapper - using !important to override any conflicting styles
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

<div class="tile-body tile-body--home-apps">
    <h2 class="home-apps-title"><?php echo esc_html( $tile->label ); ?></h2>
    <div id="home-apps-spinner-<?php echo esc_attr( $tile->handle ); ?>"
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

