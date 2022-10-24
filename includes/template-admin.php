<?php
$dt_tiles = new DT_Dashboard_Plugin_Tiles();
$shown_tiles = $dt_tiles->shown();
$hidden_tiles = $dt_tiles->hidden();
?>
<div class="wrap">
    <h2><?php esc_attr_e( 'DISCIPLE TOOLS - DASHBOARD', 'disciple-tools-dashboard' ) ?></h2>
    <div id="poststuff">
        <div id="post-body"
             class="metabox-holder columns-2">
            <div id="post-body-content">
                <p><?php echo esc_html_e( "Choose the default dashboard layout for new users.", 'disciple-tools-dashboard' ); ?></p>
                <table class="widefat striped">
                    <thead>
                    <tr>
                        <th colspan="2"><?php esc_html_e( "ACTIVE TILES", 'disciple-tools-dashboard' ) ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="sort-classes">
                    <?php foreach ( $shown_tiles as $handle => $tile ): ?>
                        <tr class="tile"
                            data-tile-handle="<?php echo esc_attr( $handle ); ?>">
                            <td class="handle"
                                width="20"><span class="dashicons dashicons-move"></span></td>
                            <td><?php echo esc_html( $tile->label ); ?></td>
                            <td align="right">
                                <form method="POST">
                                    <?php wp_nonce_field( 'hide_' .  sanitize_key( $handle ) ); ?>
                                    <input type="hidden"
                                           name="hide_tile"
                                           value="<?php echo esc_attr( sanitize_key( $handle ) ); ?>">
                                    <button class="button"
                                            type="submit"><?php echo esc_html_e( "Hide", 'disciple-tools-dashboard' ); ?></button>
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
                        <th><?php esc_html_e( "HIDDEN TILES", 'disciple-tools-dashboard' ) ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $hidden_tiles as $handle => $tile ): ?>
                        <tr>
                            <td><?php echo esc_html( $tile->label ); ?></td>
                            <td align="right">
                                <form method="POST">
                                    <?php wp_nonce_field( 'show_' .  sanitize_key( $handle ) ); ?>
                                    <input type="hidden"
                                           name="show_tile"
                                           value="<?php echo esc_attr( sanitize_key( $handle ) ); ?>">
                                    <button class="button"
                                            type="submit"><?php echo esc_html_e( "Show", 'disciple-tools-dashboard' ); ?></button>
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

