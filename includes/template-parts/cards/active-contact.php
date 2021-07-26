<div class="item" style="flex-basis: 25%;">
    <div class="card" style="height: 100%">
        <div style="display: flex; flex-direction: column; height: 100%">
            <div style="text-align: center">
                <span class="card-title">
                    <?php esc_html_e( "Active Contacts", 'disciple-tools-dashboard' ) ?>
                </span>
            </div>
            <div style="text-align: center; flex-grow: 1; margin-top: 20px">
                <span class="numberCircle">&nbsp;<span id="active_contacts">-</span>&nbsp;</span>
            </div>
            <div class="view-all" style="flex-shrink: 1">
                <a class="button dt-green" style="margin-bottom:0" href="<?php echo esc_url( home_url( '/' ) ) . "contacts/new" ?>">
                    <?php esc_html_e( "Add a contact", 'disciple-tools-dashboard' ) ?>
                </a>
                <!--                                <a class="button" style="margin-bottom:0; margin-left: 10px" href="--><?php //echo esc_url( home_url( '/' ) ) . "contacts?list-tab=active" ?><!--">-->
                <!--                                    --><?php //esc_html_e( "View Contacts List", 'disciple-tools-dashboard' ) ?>
                <!--                                </a>-->
            </div>
            <?php include __dir__ . '/remove.php' ?>
        </div>
    </div>
</div>
