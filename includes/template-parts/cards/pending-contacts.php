<div class="item" style="flex-basis: 25%;">
    <div class="card">
        <div style="display: flex; flex-direction: column; height: 100%">
            <div style="text-align: center">
                <div style="background-color: rgba(236,17,17,0.2);" class="count-square">
                    <span id="needs_accepting"></span>
                </div>
                <span class="card-title">
                                    <?php esc_html_e( "Pending Contacts", 'disciple-tools-dashboard' ) ?>
                                </span>
            </div>
            <div id="needs_accepting_list"  style="flex-grow: 1"></div>
            <!--                            <div style="flex-shrink: 1" class="view-all">-->
            <!--                                <button class="button" id="view_needs_accepted_button">--><?php //esc_html_e( "View All", 'disciple-tools-dashboard' ) ?><!--</button>-->
            <!--                            </div>-->
        </div>
        <?php include __dir__ . '/remove.php' ?>

    </div>
</div>
