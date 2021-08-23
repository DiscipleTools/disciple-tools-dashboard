<div class="card-header">
     <?php echo $card->label ?>
     <div style="display: inline-block"
          class="stats-spinner loading-spinner active"></div>
</div>
<div class="card-body">
     <p style="text-align: center; margin-bottom: 30px"><?php esc_html_e("Milestones on your active contacts", 'disciple-tools-dashboard') ?></p>
     <div style="display: flex; flex-wrap: wrap"
     id="milestones"></div>
</div>