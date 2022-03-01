<div class="card-header">
    <?php echo esc_html( $card->label ) ?>
     <div style="display: inline-block" class="stats-spinner loading-spinner active"></div>
</div>
<div class="card-body card-body--center">
    <div>
        <p style="text-align: center; display: none" id="empty_seeker_path"><strong><?php esc_html_e( "No data to show yet. You have no active contacts", 'disciple-tools-dashboard' ) ?></strong></p>
        <div id="seeker_path_chart" style="height:400px; width;200px; padding-left: 10px; padding-right: 10px"></div>
    </div>

</div>
