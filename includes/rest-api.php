<?php
/**
 * Rest API example class
 */


class DT_Dashboard_Plugin_Endpoints
{
    public $permissions = [ 'access_contacts' ];

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    private $version = 1;
    private $context = "dt-dashboard";
    private $namespace;
    public function __construct() {
        $this->namespace = $this->context . "/v" . intval( $this->version );
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    }

    public function has_permission(){
        $pass = false;
        foreach ( $this->permissions as $permission ){
            if ( current_user_can( $permission ) ){
                $pass = true;
            }
        }
        return $pass;
    }


    //See https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
    public function add_api_routes() {
        register_rest_route(
            $this->namespace, '/stats', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_other_stats' ],
            ]
        );
        register_rest_route(
            $this->namespace, '/user', [
                'methods'  => 'POST',
                'callback' => [ $this, 'update_user' ],
            ]
        );
    }


    public static function get_data(){

        $to_accept = Disciple_Tools_Contacts::search_viewable_contacts( [
            'overall_status' => [ 'assigned' ],
            'assigned_to'    => [ 'me' ]
        ] );
        $update_needed = Disciple_Tools_Contacts::search_viewable_contacts( [
            'requires_update' => [ "true" ],
            'assigned_to'     => [ 'me' ],
            'overall_status' => [ '-closed' ]
        ] );
        if ( sizeof( $update_needed["contacts"] ) > 5 ) {
            $update_needed["contacts"] = array_slice( $update_needed["contacts"], 0, 5 );
        }
        if ( sizeof( $to_accept["contacts"] ) > 5 ) {
            $to_accept["contacts"] = array_slice( $to_accept["contacts"], 0, 5 );
        }
        foreach ( $update_needed["contacts"] as &$contact ){
            $now = time();
            $last_modified = get_post_meta( $contact->ID, "last_modified", true );
            $days_different = (int) round( ( $now - (int) $last_modified ) / ( 60 * 60 * 24 ) );
            $contact->last_modified_msg = esc_attr( sprintf( __( '%s days since last update', 'disciple_tools' ), $days_different ), 'disciple_tools' );
        }
        $my_active_contacts = self::get_active_contacts();

        return [
            "active_contacts" => $my_active_contacts,
            "accept_needed" => $to_accept,
            "update_needed" => $update_needed,
        ];
    }

    public function get_other_stats(){
        if ( !$this->has_permission() ){
            return new WP_Error( __FUNCTION__, "Missing Permissions", [ 'status' => 400 ] );
        }
        $seeker_path_personal = self::query_my_contacts_progress();
        $milestones = self::milestones();
        $personal_benchmarks = self::get_personal_benchmarks();
        return [
            "benchmarks" => $personal_benchmarks,
            "seeker_path_personal" => $seeker_path_personal,
            "milestones" => $milestones
        ];
    }

    private static function get_active_contacts(){
        global $wpdb;
        $my_active_contacts = $wpdb->get_var( $wpdb->prepare( "
            SELECT count(a.ID)
              FROM $wpdb->posts as a
              INNER JOIN $wpdb->postmeta as assigned_to
                ON a.ID=assigned_to.post_id
                  AND assigned_to.meta_key = 'assigned_to'
                  AND assigned_to.meta_value = CONCAT( 'user-', %s )
                JOIN $wpdb->postmeta as b
                  ON a.ID=b.post_id
                     AND b.meta_key = 'overall_status'
                         AND b.meta_value = 'active'
                INNER JOIN $wpdb->postmeta as e
                  ON a.ID=e.post_id
                     AND (( e.meta_key = 'type'
                            AND ( e.meta_value = 'media' OR e.meta_value = 'next_gen' ) )
                          OR e.meta_key IS NULL)
              WHERE a.post_status = 'publish'
              AND post_type = 'contacts'
              ", get_current_user_id() ) );
        return $my_active_contacts;
    }

    private static function get_personal_benchmarks(){
        global $wpdb;
        $thirty_days_ago = time() - 30 * 24 * 60 * 60;
        $sixty_days_ago = $thirty_days_ago - 30 * 24 * 60 * 60;

        $contacts_current = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(DISTINCT(object_id)) 
            FROM $wpdb->dt_activity_log a
            INNER JOIN $wpdb->postmeta as type ON ( object_id = type.post_id AND type.meta_key = 'type' AND type.meta_value != 'user' )
            WHERE object_type = 'contacts' 
            AND a.meta_key = 'assigned_to'
            AND hist_time >= %s 
            AND a.meta_value = %s
        ", $thirty_days_ago, "user-" . get_current_user_id() )
        );

        $contacts_previous = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(DISTINCT(object_id)) 
            FROM $wpdb->dt_activity_log a
            INNER JOIN $wpdb->postmeta as type ON ( object_id = type.post_id AND type.meta_key = 'type' AND type.meta_value != 'user' )
            WHERE object_type = 'contacts' 
            AND a.meta_key = 'assigned_to'
            AND hist_time >= %s 
            AND hist_time < %s 
            AND a.meta_value = %s
        ", $sixty_days_ago, $thirty_days_ago, "user-" . get_current_user_id() )
        );

        $met_current = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(DISTINCT(object_id)) 
            FROM $wpdb->dt_activity_log a
            INNER JOIN $wpdb->postmeta as type ON ( object_id = type.post_id AND type.meta_key = 'type' AND type.meta_value != 'user' )
            WHERE object_type = 'contacts' 
            AND a.meta_key = 'seeker_path'
            AND hist_time >= %s 
            AND a.meta_value = 'met'
            AND user_id = %s
        ", $thirty_days_ago, get_current_user_id() )
        );

        $met_previous = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(DISTINCT(object_id))
            FROM $wpdb->dt_activity_log a
            INNER JOIN $wpdb->postmeta as type ON ( object_id = type.post_id AND type.meta_key = 'type' AND type.meta_value != 'user' )
            WHERE object_type = 'contacts' 
            AND a.meta_key = 'seeker_path'
            AND hist_time >= %s 
            AND hist_time < %s
            AND a.meta_value = 'met'
            AND user_id = %s
        ", $sixty_days_ago, $thirty_days_ago, get_current_user_id() )
        );

        $milestones_current = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(DISTINCT(object_id)) 
            FROM $wpdb->dt_activity_log a
            INNER JOIN $wpdb->postmeta as type ON ( object_id = type.post_id AND type.meta_key = 'type' AND type.meta_value != 'user' )
            WHERE object_type = 'contacts' 
            AND a.meta_key = 'milestones'
            AND hist_time >= %s 
            AND a.meta_value != 'value_deleted'
            AND user_id = %s
        ", $thirty_days_ago, get_current_user_id() )
        );

        $milestones_previous = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(*) 
            FROM $wpdb->dt_activity_log a
            INNER JOIN $wpdb->postmeta as type ON ( object_id = type.post_id AND type.meta_key = 'type' AND type.meta_value != 'user' )
            WHERE object_type = 'contacts' 
            AND a.meta_key = 'milestones'
            AND hist_time >= %s 
            AND hist_time < %s
            AND a.meta_value != 'value_deleted'
            AND user_id = %s
        ", $sixty_days_ago, $thirty_days_ago, get_current_user_id() )
        );

        return [
            "contacts" => [
                "previous" => $contacts_previous,
                "current" => $contacts_current,
            ],
            "meetings" => [
                "previous" => $met_previous,
                "current" => $met_current
            ],
            "milestones" => [
                "previous" => $milestones_previous,
                "current" => $milestones_current
            ]
        ];
    }

    public static function query_my_contacts_progress( $user_id = null ) {
        global $wpdb;
        if ( empty( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        $defaults = [];
        $contact_fields = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings();
        $seeker_path_options = $contact_fields["seeker_path"]["default"];
        foreach ( $seeker_path_options as $key => $option ) {
            $defaults[$key] = [
                'label' => $option["label"],
                'count' => 0,
            ];
        }

        $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT b.meta_value as seeker_path, count( a.ID ) as count
             FROM $wpdb->posts as a
               JOIN $wpdb->postmeta as b
                 ON a.ID=b.post_id
                    AND b.meta_key = 'seeker_path'
               JOIN $wpdb->postmeta as c
                 ON a.ID=c.post_id
                    AND c.meta_key = 'assigned_to'
                    AND c.meta_value = %s
               JOIN $wpdb->postmeta as d
                 ON a.ID=d.post_id
                    AND d.meta_key = 'overall_status'
                    AND d.meta_value = 'active'
             WHERE a.post_status = 'publish'
              AND a.post_type = 'contacts'
              AND a.ID NOT IN (
                SELECT post_id
                FROM $wpdb->postmeta
                WHERE meta_key = 'corresponds_to_user'
                AND meta_value != 0
                GROUP BY post_id
            )
             GROUP BY b.meta_value
        ",
        'user-'. $user_id ), ARRAY_A );

        $query_results = [];

        if ( ! empty( $results ) ) {
            foreach ( $results as $result ) {
                if ( isset( $defaults[$result['seeker_path']] ) ) {
                    $query_results[$result['seeker_path']] = [
                        'label' => $defaults[$result['seeker_path']]['label'],
                        'count' => intval( $result['count'] ),
                    ];
                }
            }
        }
        $query_results = wp_parse_args( $query_results, $defaults );
        $res = [];
        foreach ( $query_results as $r ){
            $res[] = [
                "label" => $r['label'],
                "value" => $r["count"]
            ];
        }

        return $res;
    }


    public static function query_project_contacts_progress() {
        global $wpdb;


        $results = $wpdb->get_results( "
            SELECT b.meta_value as seeker_path, count( a.ID ) as count
             FROM $wpdb->posts as a
               JOIN $wpdb->postmeta as b
                 ON a.ID=b.post_id
                    AND b.meta_key = 'seeker_path'
               JOIN $wpdb->postmeta as d
                 ON a.ID=d.post_id
                    AND d.meta_key = 'overall_status'
                    AND d.meta_value = 'active'
             WHERE a.post_status = 'publish'
                AND a.post_type = 'contacts'
                AND a.ID NOT IN (
                    SELECT post_id
                    FROM $wpdb->postmeta
                    WHERE meta_key = 'corresponds_to_user'
                      AND meta_value != 0
                    GROUP BY post_id
                )
             GROUP BY b.meta_value
        ", ARRAY_A );

        $query_results = [];

        $contact_fields = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings();
        $seeker_path_options = $contact_fields["seeker_path"]["default"];

        foreach ( $seeker_path_options as $seeker_path_key => $seeker_path_option ){
            $added = false;
            foreach ( $results as $result ) {
                if ( $result["seeker_path"] == $seeker_path_key ){
                    $query_results[] = [
                        'key' => $seeker_path_key,
                        'label' => $seeker_path_option['label'],
                        'value' => intval( $result['count'] )
                    ];
                    $added = true;
                }
            }
            if ( !$added ){
                $query_results[] = [
                    'key' => $seeker_path_key,
                    'label' => $seeker_path_option['label'],
                    'value' => 0
                ];
            }
        }

        return $query_results;
    }

    public static function milestones(){
        global $wpdb;

        $user_id = get_current_user_id();
        $res = $wpdb->get_results( $wpdb->prepare( "SELECT b.meta_value as milestones, count( a.ID ) as value
             FROM $wpdb->posts as a
               JOIN $wpdb->postmeta as b
                 ON a.ID=b.post_id
                    AND b.meta_key = 'milestones'
               JOIN $wpdb->postmeta as c
                 ON a.ID=c.post_id
                    AND c.meta_key = 'assigned_to'
                        AND c.meta_value = %s
               JOIN $wpdb->postmeta as d
                 ON a.ID=d.post_id
                    AND d.meta_key = 'overall_status'
                        AND d.meta_value = 'active'
             WHERE a.post_status = 'publish'
                AND a.post_type = 'contacts'
                AND a.ID NOT IN (
                    SELECT post_id
                    FROM $wpdb->postmeta
                    WHERE meta_key = 'corresponds_to_user'
                    AND meta_value != 0
                    GROUP BY post_id
                )
             GROUP BY b.meta_value
        ",
        'user-'. $user_id ), ARRAY_A );

        $field_settings = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings();
        $milestones_options = $field_settings["milestones"]["default"];
        $milestones_data = [];

        foreach ( $milestones_options as $option_key => $option_value ){
            $milestones_data[$option_value["label"]] = 0;
            foreach ( $res as $r ){
                if ( $r["milestones"] === $option_key ){
                    $milestones_data[$option_value["label"]] = $r["value"];
                }
            }
        }
        $return = [];
        foreach ( $milestones_data as $k => $v ){
            $return[] = [
                "milestones" => $k,
                "value" => (int) $v
            ];
        }

        return $return;
    }

    public static function translations(){
        return [
            "accept" => __( "Accept", 'disciple_tools' ),
            "decline" => __( "Decline", 'disciple_tools' ),
            "number_contacts_assigned" => __( "# Contacts Assigned", 'disciple_tools' ),
            "number_meetings" => __( "# First Meetings", 'disciple_tools' ),
            "number_milestones" => __( "# Faith milestones", 'disciple_tools' ),
        ];
    }

    public function update_user( WP_REST_Request $request ){
        if ( !$this->has_permission() ){
            return new WP_Error( __FUNCTION__, "Missing Permissions", [ 'status' => 400 ] );
        }
        $get_params = $request->get_params();
        $body = $request->get_json_params();
        $user = wp_get_current_user();
        if ( !empty( $body["workload_status"] ) ) {
            update_user_option( $user->ID, 'workload_status', $body["workload_status"] );
        }

        return true;
    }
}
