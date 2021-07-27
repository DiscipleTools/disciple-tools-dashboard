<div class="dash-card" data-card-handle="<?php echo $card->handle; ?>" id="dash-card--<?php echo $card->handle; ?>">
    <?php $card->render(); ?>
    <span class="card-remove"
          onclick="dt_dashboard.remove('<?php echo $card->handle; ?>')">X</span>
</div>
