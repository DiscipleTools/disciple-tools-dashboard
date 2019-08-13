<?php
declare(strict_types=1);

$url = dt_get_url_path();
$dt_post_type = explode( "/", $url )[0];

if ( ! current_user_can( 'access_contacts' ) ) {
    wp_die( esc_html( "Permission denied" ), "Permission denied", 403 );
}

get_header();

?>

    <div id="content" class="dashboard-page">
        <div id="inner-content">
            <div class="dash-cards">
                <div class="item" style="flex-basis: 33%;">
                    <div class="card" style="height: 100%">
                        <div style="display: flex; flex-direction: column; height: 100%">
                            <div style="text-align: center">
                                <span class="card-title"><?php esc_html_e( "Active Contacts", 'disciple_tools' ) ?></span>
                            </div>
                            <div style="text-align: center; flex-grow: 1; margin-top: 20px">
                                <span class="numberCircle">&nbsp;<span id="active_contacts">-</span>&nbsp;</span>
                            </div>
                            <div class="view-all" style="flex-shrink: 1">
                                <a class="button dt-green" style="margin-bottom:0" href="<?php echo esc_url( home_url( '/' ) ) . "contacts/new" ?>">
                                    <?php esc_html_e( "Add a contact", 'disciple_tools' ) ?>
                                </a>
                                <a class="button" style="margin-bottom:0; margin-left: 10px" href="<?php echo esc_url( home_url( '/' ) ) . "contacts?list-tab=active" ?>">
                                    <?php esc_html_e( "View Contacts List", 'disciple_tools' ) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="item" style="flex-basis: 33%;">
                    <div class="card">
                        <div style="display: flex; flex-direction: column; height: 100%">
                            <div>
                                <div style="background-color: rgba(236,17,17,0.2);" class="count-square">
                                    <span id="needs_accepting"></span>
                                </div>
                                <span class="card-title">
                                    <?php esc_html_e( "Pending Contacts", 'disciple_tools' ) ?>
                                </span>
                            </div>
                            <div id="needs_accepting_list"  style="flex-grow: 1"></div>
                            <div style="flex-shrink: 1" class="view-all">
                                <button class="button" id="view_needs_accepted_button"><?php esc_html_e( "View All", 'disciple_tools' ) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item" style="flex-basis: 33%;">
                    <div class="card">
                        <div style="display: flex; flex-direction: column; height: 100%">
                            <div>
                                <div style="background-color: rgba(236,17,17,0.2);" class="count-square">
                                    <span id="update_needed"></span>
                                </div>
                                <span class="card-title">
                                    <?php esc_html_e( "Update Needed", 'disciple_tools' ) ?>
                                </span>
                            </div>
                            <div id="update_needed_list" style="flex-grow: 1"></div>
                            <div class="view-all" style="flex-shrink: 1">
                                <button class="button" id="view_updated_needed_button"><?php esc_html_e( "View All", 'disciple_tools' ) ?></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>





            <div class="dash-cards" id="benchmarks">
                <div class="item" style="flex-basis: 100%">
                <div class="card">
                    <div style="display: flex; flex-wrap: wrap">
                        <div style="flex-basis: 40%">
                            <h2 style="margin:50px; display: inline-block"><?php esc_html_e( "Personal Benchmarks", 'disciple_tools' ) ?>
                                <div style="display: inline-block" class="loading-spinner active"></div>
                            </h2>

                            <ul style="list-style: none; margin-left: 50px">
                                <li>
                                    <div style="background-color: #C7E3FF; border-radius: 5px; height: 20px; width:20px; display: inline-block"></div>
                                    <span id="benchmarks_current" style="vertical-align: text-bottom"></span>
                                </li>
                                <li>
                                    <div style="background-color: #3f729b; border-radius: 5px; height: 20px; width:20px; display: inline-block"></div>
                                    <span id="benchmarks_previous" style="vertical-align: text-bottom"></span>
                                </li>
                            </ul>

                        </div>
                        <div style="flex-basis: 60%">
                            <div id="benchmark_chart" style="height: 300px"></div>
                        </div>
                    </div>
                </div>
                </div>
            </div>



            <div class="dash-cards">
                <div class="item" style="flex-basis: 50%">
                    <div class="card">
                        <span class="card-title" style="text-align: center; margin-bottom: 15px">
                            <?php echo esc_html__( 'Faith Milestone Totals', 'disciple_tools' ) ?>
                             <div style="display: inline-block" class="loading-spinner active"></div>
                        </span>
                        <p style="text-align: center; margin-bottom: 30px"><?php esc_html_e( "Milestones on your active contacts", 'disciple_tools' ) ?></p>
                        <div >
                            <div style="display: flex; flex-wrap: wrap" id="milestones">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="item" style="flex-basis: 50%">
                    <div class="card">
                        <span class="card-title" style="text-align: center; margin-bottom: 15px">
                            <?php esc_html_e( "Seeker Path Progress", 'disciple_tools' ) ?>
                             <div style="display: inline-block" class="loading-spinner active"></div>
                        </span>
                        <p style="text-align: center; display: none" id="empty_seeker_path"><strong><?php esc_html_e( "No data to show yet. You have no active contacts", 'disciple_tools' ) ?></strong></p>
                        <div id="seeker_path_chart" style="height:400px; width;200px; padding-left: 10px; padding-right: 10px"></div>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        jQuery(function($) {

        });
    </script>


<?php
get_footer();
