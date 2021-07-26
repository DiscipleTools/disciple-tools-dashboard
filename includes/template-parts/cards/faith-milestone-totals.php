<div class="item" style="flex-basis: 50%">
    <div class="card">
                        <span class="card-title" style="text-align: center; margin-bottom: 15px">
                            <?php echo esc_html__( 'Faith Milestone Totals', 'disciple-tools-dashboard' ) ?>
                             <div style="display: inline-block" class="stats-spinner loading-spinner active"></div>
                        </span>
        <p style="text-align: center; margin-bottom: 30px"><?php esc_html_e( "Milestones on your active contacts", 'disciple-tools-dashboard' ) ?></p>
        <div >
            <div style="display: flex; flex-wrap: wrap" id="milestones">

            </div>
        </div>
    </div>
    <?php
    include __dir__ . '/remove.php'
    ?>
</div>
