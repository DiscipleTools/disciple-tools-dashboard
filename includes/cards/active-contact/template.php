<div class="item"
     style="flex-basis: 25%;">
    <div class="card"
         style="height: 100%">
        <div style="display: flex; flex-direction: column; height: 100%">
            <div style="text-align: center">
                <span class="card-title">
                    <?php esc_html_e("Active Contacts", 'disciple-tools-dashboard') ?>
                </span>
            </div>
            <div style="text-align: center; flex-grow: 1; margin-top: 20px">
                <span class="numberCircle">&nbsp;<span id="active_contacts">-</span>&nbsp;</span>
            </div>
            <div class="view-all"
                 style="flex-shrink: 1">
                <a class="button dt-green"
                   style="margin-bottom:0"
                   href="<?php echo esc_url(home_url('/')) . "contacts/new" ?>">
                    <?php esc_html_e("Add a contact", 'disciple-tools-dashboard') ?>
                </a>
            </div>
        </div>
    </div>
</div>
