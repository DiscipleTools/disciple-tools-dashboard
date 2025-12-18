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
?>

<div class="tile-header">
    <?php echo esc_html( $tile->label ); ?>
    <div id="home-apps-spinner-<?php echo esc_attr( $tile->handle ); ?>"
         style="display: inline-block"
         class="stats-spinner loading-spinner active">
    </div>
</div>
<div class="tile-body tile-body--home-apps">
    <div class="home-apps-carousel-wrapper">
        <div class="home-apps-carousel" 
             id="home-apps-carousel-<?php echo esc_attr( $tile->handle ); ?>"
             data-apps='<?php echo esc_attr( wp_json_encode( $apps ) ); ?>'>
            <!-- Apps will be rendered here via JavaScript -->
        </div>
    </div>
</div>

