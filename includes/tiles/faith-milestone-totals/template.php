<div class="tile-header">
    <?php echo esc_html( $tile->label ) ?>
    <div style="display: inline-block"
         class="stats-spinner loading-spinner active"></div>
</div>
<div class="tile-subheader">
    <?php esc_html_e( "Milestones on your active contacts", 'disciple-tools-dashboard' ) ?>
</div>
<div class="tile-body tile-body--scroll">
    <div id="milestones"></div>
</div>
