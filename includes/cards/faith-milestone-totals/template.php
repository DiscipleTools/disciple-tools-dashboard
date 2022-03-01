<div class="card-header">
    <?php echo esc_html( $card->label ) ?>
    <div style="display: inline-block"
         class="stats-spinner loading-spinner active"></div>
</div>
<div class="card-subheader">
    <?php esc_html_e( "Milestones on your active contacts", 'disciple-tools-dashboard' ) ?>
</div>
<div class="card-body card-body--scroll">
    <div id="milestones"></div>
</div>
