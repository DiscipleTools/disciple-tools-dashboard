<?php
declare(strict_types=1);

$url = dt_get_url_path();
$dt_post_type = explode( "/", $url )[0];
$dt_cards = new DT_Dashboard_Plugin_User_Cards();
$cards = $dt_cards->shown();
$hidden_cards = $dt_cards->hidden();
dt_please_log_in();

if ( ! current_user_can( 'access_contacts' ) ) {
    wp_die( esc_html( "Permission denied" ), "Permission denied", 403 );
}

get_header();

?>
    <div id="content" class="dashboard-page">
        <div id="inner-content">
            <div class="dash-cards">
                    <?php foreach($cards as $card): ?>
                        <?php $card->render(); ?>
                    <?php endforeach; ?>
            </div>

            <h2>Add</h2>
            <?php foreach($hidden_cards as $card): ?>
                <li class="card-show" data-card-handle="<?php echo $card->handle; ?>"><?php echo $card->label; ?></li>
            <?php endforeach; ?>
            <ul>

            </ul>
        </div>

    </div>
<?php
get_footer();
