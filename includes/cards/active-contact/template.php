<div class="card-header">
    <?php echo $card->label ?>
</div>
<div class="card-body">
    <div style="text-align: center; flex-grow: 1; margin-top: 40px">
        <span class="numberCircle">&nbsp;<span id="active_contacts">-</span>&nbsp;</span>
    </div>
    <div class="view-all">
        <a class="button" href="<?php echo esc_url(home_url('/')) . "contacts/new" ?>">
            <?php esc_html_e("Add a contact", 'disciple-tools-dashboard') ?>
        </a>
    </div>
</div>