<?php
declare(strict_types=1);

$url = dt_get_url_path();
$dt_post_type = explode( "/", $url )[0];
$dt_tiles = new DT_Dashboard_Plugin_User_Tiles();
$tiles = $dt_tiles->all();
$shown_tiles = $dt_tiles->shown();
$hidden_tiles = $dt_tiles->hidden();
dt_please_log_in();

if ( ! current_user_can( 'access_disciple_tools' ) ) {
    wp_die( esc_html( "Permission denied" ), "Permission denied", 403 );
}

get_header();

?>
<div id="blank"></div>
    <div id="dashboard">

        <div id="content" class="dashboard-page">
            <div id="inner-content">
                <div class="dash-tiles" id="dash-tiles">
                    <?php foreach ( $shown_tiles as $tile ): ?>
                        <?php include __DIR__ . '/template-parts/tile.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        function closeTileNav($id) {
            var element = document.getElementById("tile-nav-" + $id);
            element.classList.remove("show");
        }

        function toggleTileNav($id) {
            var element = document.getElementById("tile-nav-" + $id);
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
