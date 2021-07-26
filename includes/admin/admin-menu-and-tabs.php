<?php
/**
 * DT_Dashboard_Plugin_Menu class for the admin page
 *
 * @class       DT_Dashboard_Plugin_Menu
 * @version     0.1.0
 * @since       0.1.0
 */


if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Initialize menu class
 */
DT_Dashboard_Plugin_Menu::instance();

/**
 * Class DT_Dashboard_Plugin_Menu
 */
class DT_Dashboard_Plugin_Menu {

    public $token = 'dt_dashboard_plugin';

    private static $_instance = null;

    /**
     * DT_Dashboard_Plugin_Menu Instance
     *
     * Ensures only one instance of DT_Dashboard_Plugin_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_Dashboard_Plugin_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {

        add_action( "admin_menu", array( $this, "register_menu" ) );
        add_action( 'admin_enqueue_scripts', function() {
            $this->scripts();
        }, 1 );
    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', __( 'Dashboard', 'dt_dashboard_plugin' ), __( 'Dashboard', 'dt_dashboard_plugin' ), 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    public function scripts() {
        wp_localize_script( 'wp-api', 'dashboardWPApiShare', array(
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'root' => esc_url_raw( rest_url() ) . 'dt-dashboard'
        ));
        wp_enqueue_script('jquery');
        wp_register_script('jquery-ui','https://code.jquery.com/ui/1.12.1/jquery-ui.min.js',array('jquery'));
        wp_enqueue_script('jquery-ui');
        wp_enqueue_script( 'dt-admin', DT_Dashboard_Plugin::path() . 'includes/admin.js', [
            'wp-api',
            'jquery',
            'jquery-ui',
        ], filemtime( DT_Dashboard_Plugin::dir() . 'includes/admin.js' ), true );
    }

    /**
     * Builds page contents
     * @since 0.1
     */

    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.' ) );
        }

        $this->update();

        include DT_Dashboard_Plugin::includes_dir() . 'template-admin.php';
    }

    /**
     * Make updates before displaing.
     */
    public function update() {
        $cards = new DT_Dashboard_Plugin_Cards();

        if ( isset( $_POST["show_card"] ) ) {
            $cards->show($_POST["show_card"]);
        }

        if ( isset( $_POST["hide_card"] ) ) {
            $cards->hide($_POST["hide_card"]);
        }

        if ( isset( $_POST["card_sort"] ) ) {
            $cards->sort($_POST["card_sort"]);
        }
    }
}
