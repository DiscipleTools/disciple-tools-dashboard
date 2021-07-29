<?php
$dt_cards = new DT_Dashboard_Plugin_Cards();
$shown_cards = $dt_cards->shown();
$hidden_cards = $dt_cards->hidden();
?>
<div class="wrap">
    <h2><?php esc_attr_e( 'DISCIPLE TOOLS - DASHBOARD', 'disciple-tools-dashboard' ) ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <p>Choose the default dashboard layout for new users.</p>
                <table class="widefat striped">
                    <thead>
                    <tr>
                        <th colspan="2"><?php esc_html_e( "ACTIVE CARDS", 'disciple-tools-dashboard' ) ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="sort-classes">
                    <?php foreach($shown_cards as $handle => $card): ?>
                        <tr class="card" data-card-handle="<?php echo $handle; ?>">
                            <td class="handle" width="20"><span class="dashicons dashicons-move"></span></td>
                            <td><?php echo $card->label; ?></td>
                            <td align="right">
                                <form method="POST">
                                    <input type="hidden" name="hide_card" value="<?php echo $handle; ?>">
                                    <button class="button" type="submit">Hide</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <table class="widefat striped">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( "HIDDEN CARDS", 'disciple-tools-dashboard' ) ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($hidden_cards as $handle => $card): ?>
                        <tr>
                            <td><?php echo $card->label; ?></td>
                            <td align="right">
                                <form method="POST">
                                    <input type="hidden" name="show_card" value="<?php echo $handle; ?>">
                                    <button class="button" type="submit">Show</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div><!-- end post-body-content -->
        </div><!-- post-body meta box container -->
    </div><!--poststuff end -->
</div><!-- wrap end -->

