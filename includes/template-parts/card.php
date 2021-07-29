<div class="dash-card item"
     data-card-handle="<?php echo $card->handle; ?>"
     id="dash-card--<?php echo $card->handle; ?>"
     style="--dashboard-card-column: span <?php echo $card->span; ?>;"
>
    <div class="card">
        <?php $card->render(); ?>

        <div class="card-remove"
             onclick="dt_dashboard.moveBack('<?php echo $card->handle; ?>')"><</div>
        <div class="card-remove"
             onclick="dt_dashboard.moveForward('<?php echo $card->handle; ?>')">></div>
        <div class="card-remove"
              onclick="dt_dashboard.remove('<?php echo $card->handle; ?>')">X</div>
    </div>
</div>
