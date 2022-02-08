<?php
declare(strict_types=1);

$url = dt_get_url_path();
$dt_post_type = explode( "/", $url )[0];
$dt_cards = new DT_Dashboard_Plugin_User_Cards();
$cards = $dt_cards->all();
$shown_cards = $dt_cards->shown();
$hidden_cards = $dt_cards->hidden();
dt_please_log_in();

if ( ! current_user_can( 'access_contacts' ) ) {
    wp_die( esc_html( "Permission denied" ), "Permission denied", 403 );
}

get_header();

?>
<div id="blank"></div>
    <div id="dashboard">

        <div id="content" class="dashboard-page">
            <div id="inner-content">
                <div class="dash-cards" id="dash-cards">
                    <?php foreach ($shown_cards as $card): ?>
                        <?php include __DIR__ . '/template-parts/card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        function closeCardNav($id) {
            var element = document.getElementById("card-nav-" + $id);
            element.classList.remove("show");
        }

        function toggleCardNav($id) {
            var element = document.getElementById("card-nav-" + $id);
            element.classList.toggle("show");

            var blank = document.getElementById("blank");
            blank.classList.toggle("show");

            blank.onclick = function () {
                element.classList.toggle("show");
                blank.classList.toggle("show");
            };

        }
    </script>
<?php
get_footer();
