<?php
/**
 *Plugin Name: Disciple.Tools - Dashboard
 * Plugin URI: https://github.com/DiscipleTools/disciple-tools-dashboard
 * Description: The multiplier dashboard upgrades the multipliers experience as soon as they log into the system giving them a landing page with stats.
 * Version:  1.2.5
 * Author URI: https://github.com/DiscipleTools
 * GitHub Plugin URI: https://github.com/DiscipleTools/disciple-tools-dashboard
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */
require_once( 'includes/tiles/tile.php' );
require_once( 'includes/tiles/tiles.php' );
require_once( 'includes/tiles/tiles-user.php' );

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$dt_dashboard_required_dt_theme_version = '1.0.0';

/**
 * Gets the instance of the `DT_Dashboard_Plugin` class.
 *
 * @since  0.1
 * @access public
 * @return object|bool
 */
add_action( 'after_setup_theme', function() {
    global $dt_dashboard_required_dt_theme_version;
    $wp_theme = wp_get_theme();
    $version = $wp_theme->version;
    /*
     * Check if the Disciple.Tools theme is loaded and is the latest required version
     */
    $is_theme_dt = class_exists( "Disciple_Tools" );

    if ( !$is_theme_dt || version_compare( $version, $dt_dashboard_required_dt_theme_version, "<" ) ) {
        if ( ! is_multisite() ) {
            add_action( 'admin_notices', 'dt_dashboard_plugin_hook_admin_notice' );
            add_action( 'wp_ajax_dismissed_notice_handler', 'dt_hook_ajax_notice_handler' );
        }

        return false;
    }
    /**
     * Load useful function from the theme
     */
    if ( !defined( 'DT_FUNCTIONS_READY' ) ){
        require_once get_template_directory() . '/dt-core/global-functions.php';
    }
    /*
     * Don't load the plugin on every rest request. Only those with the 'sample' namespace
     */
    $is_rest = dt_is_rest();
    if ( !$is_rest || strpos( dt_get_url_path(), 'dashboard' ) !== false ){
        return DT_Dashboard_Plugin::get_instance();
    }

    return false;
}, 50 );

//register the D.T Plugin
add_filter( 'dt_plugins', function ( $plugins ){
    $plugin_data = get_file_data( __FILE__, [ 'Version' => 'Version', 'Plugin Name' => 'Plugin Name' ], false );
    $plugins['disciple-tools-dashboard'] = [
        'plugin_url' => trailingslashit( plugin_dir_url( __FILE__ ) ),
        'version' => $plugin_data['Version'] ?? null,
        'name' => $plugin_data['Plugin Name'] ?? null,
    ];
    return $plugins;
});

/**
 * Singleton class for setting up the plugin.
 *
 * @since  0.1
 * @access public
 */
class DT_Dashboard_Plugin {

    /**
     * Declares public variables
     *
     * @since  0.1
     * @access public
     * @return object
     */
    public $token;
    public $version;
    public $dir_path = '';
    public $dir_uri = '';
    public $img_uri = '';
    public $includes_path;

    /**
     * Returns the instance.
     *
     * @since  0.1
     * @access public
     * @return object
     */
    public static function get_instance() {

        static $instance = null;

        if ( is_null( $instance ) ) {
            $instance = new dt_dashboard_plugin();
            $instance->setup();
            $instance->includes();
            $instance->setup_actions();

        }
        return $instance;
    }

    /**
     * Get the plugin directory.
     *
     * @since 0.3.3
     * @return string
     */
    public static function dir() {
        return __DIR__ . '/';
    }

    /**
     * Get the plugin directory.
     *
     * @since 0.3.3
     * @return string
     */
    public static function includes_dir() {
        return self::dir() . 'includes/';
    }

    /**
     * Get the plugin directory.
     *
     * @since 0.3.3
     * @return string
     */
    public static function path() {
        return plugin_dir_url( __FILE__ );
    }

    /**
     * Loads files needed by the plugin.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    private function includes() {
        require_once( 'includes/helpers.php' );
        require_once( 'includes/admin/admin-menu-and-tabs.php' );

        require_once( 'includes/rest-api.php' );
        DT_Dashboard_Plugin_Endpoints::instance();


        require_once( 'includes/functions.php' );
        DT_Dashboard_Plugin_Functions::instance();

        require_once( 'includes/tiles/tile.php' );
        require_once( 'includes/tiles/plugin-tile.php' );
        require_once( 'includes/tiles/callback-tile.php' );
    }

    /**
     * Sets up globals.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    private function setup() {

        // Main plugin directory path and URI.
        $this->dir_path     = trailingslashit( plugin_dir_path( __FILE__ ) );
        $this->dir_uri      = trailingslashit( plugin_dir_url( __FILE__ ) );


        // Admin and settings variables
        $this->token             = 'dt_dashboard_plugin';
        $this->version             = '0.2';

    }

    /**
     * Sets up main plugin actions and filters.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    private function setup_actions() {
        // Internationalize the text strings used.
        add_action( 'after_setup_theme', array( $this, 'i18n' ), 51 );
        add_action( 'after_setup_theme', [ $this, 'register_tiles' ], 100 );
    }

    /**
     * Register the dashboard tiles
     *
     * @since  1.0
     * @access private
     * @return void
     */
    public function register_tiles() {
        $tiles = DT_Dashboard_Plugin_Tiles::instance();
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Active_Contact', __( 'Active Contacts', 'disciple-tools-dashboard' ), [ 'priority' => 0 ] ) );
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Update_Needed', __( 'Update Needed', 'disciple-tools-dashboard' ), [ 'priority' => 1 ] ) );
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Pending_Contacts', __( 'Pending Contacts', 'disciple-tools-dashboard' ), [ 'priority' => 2 ] ) );
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Contact_Workload', __( 'Contact Workload', 'disciple-tools-dashboard' ), [ 'priority' => 3 ] ) );
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Faith_Milestone_Totals', __( 'Faith Milestone Totals', 'disciple-tools-dashboard' ), [
            'span' => 2,
            'priority' => 4
        ] ) );
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Seeker_Path_Progress', __( 'Seeker Path Progress', 'disciple-tools-dashboard' ), [
            'span' => 2,
            'priority' => 5
        ] ) );
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Personal_Benchmarks', __( 'Personal Benchmarks', 'disciple-tools-dashboard' ), [
            'span' => 4,
            'priority' => 6
        ] ) );
        $tiles->register( new DT_Dashboard_Plugin_Tile( 'DT_Dashboard_Plugin_Tasks', __( 'Tasks', 'disciple-tools-dashboard' ), [
            'span' => 2,
            'priority' => 7
        ] ) );
    }

    /**
     * Method that runs only when the plugin is activated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function activation() {

        // Confirm 'Administrator' has 'manage_dt' privilege. This is key in 'remote' configuration when
        // Disciple.Tools theme is not installed, otherwise this will already have been installed by the Disciple.Tools Theme
        $role = get_role( 'administrator' );
        if ( !empty( $role ) ) {
            $role->add_cap( 'manage_dt' ); // gives access to dt plugin options
        }

    }

    /**
     * Method that runs only when the plugin is deactivated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function deactivation() {
        delete_option( 'dismissed-dt-dashboard' );
    }

    /**
     * Loads the translation files.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function i18n() {
        $domain = 'disciple-tools-dashboard';
        $locale = apply_filters(
            'plugin_locale',
            ( is_admin() && function_exists( 'get_user_locale' ) ) ? get_user_locale() : get_locale(),
            $domain
        );

        $mo_file = $domain . '-' . $locale . '.mo';
        $path = realpath( dirname( __FILE__ ) . '/languages' );

        if ( $path && file_exists( $path ) ) {
            load_textdomain( $domain, $path . '/' . $mo_file );
        }
    }

    /**
     * Magic method to output a string if trying to use the object as a string.
     *
     * @since  0.1
     * @access public
     * @return string
     */
    public function __toString() {
        return 'disciple-tools-dashboard';
    }

    /**
     * Magic method to keep the object from being cloned.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'disciple-tools-dashboard' ), '0.1' );
    }

    /**
     * Magic method to keep the object from being unserialized.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'disciple-tools-dashboard' ), '0.1' );
    }

    /**
     * Magic method to prevent a fatal error when calling a method that doesn't exist.
     *
     * @since  0.1
     * @access public
     * @return null
     */
    public function __call( $method = '', $args = array() ) {
        // @codingStandardsIgnoreLine
        _doing_it_wrong( "dt_dashboard_plugin::{$method}", esc_html__( 'Method does not exist.', 'disciple-tools-dashboard' ), '0.1' );
        unset( $method, $args );
        return null;
    }
}
// end main plugin class

// Register activation hook.
register_activation_hook( __FILE__, [ 'DT_Dashboard_Plugin', 'activation' ] );
register_deactivation_hook( __FILE__, [ 'DT_Dashboard_Plugin', 'deactivation' ] );

function dt_dashboard_plugin_hook_admin_notice() {
    global $dt_dashboard_required_dt_theme_version;
    $wp_theme = wp_get_theme();
    $current_version = $wp_theme->version;
    $message = "'Disciple.Tools - Dashboard' plugin requires 'Disciple.Tools' theme to work. Please activate 'Disciple.Tools' theme or make sure it is latest version.";
    if ( $wp_theme->get_template() === 'disciple-tools-theme' ){
        $message .= ' ' . sprintf( esc_html( 'Current Disciple.Tools version: %1$s, required version: %2$s' ), esc_html( $current_version ), esc_html( $dt_dashboard_required_dt_theme_version ) );
    }
    // Check if it's been dismissed...
    if ( ! get_option( 'dismissed-dt-dashboard', false ) ) { ?>
        <div class="notice notice-error notice-dt-dashboard is-dismissible" data-notice="dt-dashboard">
            <p><?php echo esc_html( $message );?></p>
        </div>
        <script>
            jQuery(function($) {
                $( document ).on( 'click', '.notice-dt-dashboard .notice-dismiss', function () {
                    $.ajax( ajaxurl, {
                        type: 'POST',
                        data: {
                            action: 'dismissed_notice_handler',
                            type: 'dt-dashboard',
                            security: '<?php echo esc_html( wp_create_nonce( 'wp_rest_dismiss' ) ) ?>'
                        }
                    })
                });
            });
        </script>
    <?php }
}


/**
 * AJAX handler to store the state of dismissible notices.
 */
if ( !function_exists( "dt_hook_ajax_notice_handler" ) ){
    function dt_hook_ajax_notice_handler(){
        check_ajax_referer( 'wp_rest_dismiss', 'security' );
        if ( isset( $_POST["type"] ) ){
            $type = sanitize_text_field( wp_unslash( $_POST["type"] ) );
            update_option( 'dismissed-' . $type, true );
        }
    }
}


/**
 * Check for plugin updates even when the active theme is not Disciple.Tools
 *
 * Below is the publicly hosted .json file that carries the version information. This file can be hosted
 * anywhere as long as it is publicly accessible. You can download the version file listed below and use it as
 * a template.
 * Also, see the instructions for version updating to understand the steps involved.
 * @see https://github.com/DiscipleTools/disciple-tools-version-control/wiki/How-to-Update-the-Starter-Plugin
 */
add_action( 'plugins_loaded', function (){
    if ( is_admin() && !( is_multisite() && class_exists( "DT_Multisite" ) ) || wp_doing_cron() ){
        if ( ! class_exists( 'Puc_v4_Factory' ) ) {
            // find the Disciple.Tools theme and load the plugin update checker.
            foreach ( wp_get_themes() as $theme ){
                if ( $theme->get( 'TextDomain' ) === "disciple_tools" && file_exists( $theme->get_stylesheet_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' ) ){
                    require( $theme->get_stylesheet_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' );
                }
            }
        }
        if ( class_exists( 'Puc_v4_Factory' ) ){
            Puc_v4_Factory::buildUpdateChecker(
                'https://raw.githubusercontent.com/DiscipleTools/disciple-tools-dashboard/master/version-control.json',
                __FILE__,
                'disciple-tools-dashboard'
            );

        }
    }
} );
