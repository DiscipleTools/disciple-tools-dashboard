<div class="card-header">
    <?php echo $card->label ?>
    <div id="workload-spinner"
        style="display: inline-block"
        class="loading-spinner"></div>
</div>
<div class="card-body card-body--scroll">
    <p><?php esc_html_e("Choose an option to let the dispatcher(s) know if you are ready for new contacts", 'disciple-tools-dashboard') ?></p>

    <?php $options = dt_get_site_custom_lists()["user_workload_status"] ?? [];
    foreach ($options as $option_key => $option_val) :
        $icon = $option_key === "active" ? "play" : ($option_key === "existing" ? "pause" : "stop"); ?>
        <button class="button hollow status-button <?php echo esc_html($option_key) ?>-btn" name="<?php echo esc_html($option_key) ?>">
            <i class="fi-<?php echo esc_html($icon) ?>"></i> 
            <span class="btn-label"><?php echo esc_html($option_val["label"]) ?></span>
        </button>
    <?php endforeach; ?>
    
    <a href="<?php echo esc_html(site_url('settings/#availability')) ?>">
        <i class="fi-clock"></i>
        <?php esc_html_e("Set travel or dates unavailable", 'disciple-tools-dashboard') ?>
    </a>
</div>