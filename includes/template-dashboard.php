<?php
declare(strict_types=1);

$url = dt_get_url_path();
$dt_post_type = explode( "/", $url )[0];
dt_please_log_in();

if ( ! current_user_can( 'access_contacts' ) ) {
    wp_die( esc_html( "Permission denied" ), "Permission denied", 403 );
}

get_header();

?>
    <div id="content" class="dashboard-page">
        <div id="inner-content">
            <div class="dash-cards">
                <?php
                    foreach ($cards as $card) {
                        $card->render();
                    }
                ?>
            </div>
        </div>

    </div>
<?php
get_footer();
