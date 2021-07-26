<div class="item" style="flex-basis: 25%;">
    <div class="card">
        <div style="display: flex; flex-direction: column; height: 100%">
            <div style="text-align: center">
                <div style="background-color: rgba(236,17,17,0.2);" class="count-square">
                    <span id="update_needed"></span>
                </div>
                <span class="card-title">
                                    <?php esc_html_e( "Update Needed", 'disciple-tools-dashboard' ) ?>
                                </span>
            </div>
            <div id="update_needed_list" style="flex-grow: 1"></div>
            <!--                            <div class="view-all" style="flex-shrink: 1">-->
            <!--                                <button class="button" id="view_updated_needed_button">--><?php //esc_html_e( "View All", 'disciple-tools-dashboard' ) ?><!--</button>-->
            <!--                            </div>-->
        </div>
    </div>
    <?php
    include __dir__ . '/remove.php'
    ?>
</div>
