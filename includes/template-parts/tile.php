<div class="dash-tile item dash-tile--<?php echo esc_attr( $tile->handle ); ?>"
     data-tile-handle="<?php echo esc_attr( $tile->handle ); ?>"
     id="dash-tile--<?php echo esc_attr( $tile->handle ); ?>"
     <?php if ( $tile->span ): ?> style="--dashboard-tile-column: span <?php echo esc_attr( $tile->span ); ?> <?php endif; ?>"
>

    <div class="card tile">
        <?php $tile->render(); ?>
        <div class="card-footer tile-footer">
               <span class="tile-nav-btn"
                     onclick="toggleTileNav('<?php echo esc_attr( $tile->handle ); ?>')">
                   <svg height="20"
                        viewBox="0 0 4 20"
                        width="20"
                        xmlns="http://www.w3.org/2000/svg"><circle cx="2"
                                                                   cy="2"
                                                                   r="2"/><circle cx="2"
                                                                                  cy="10"
                                                                                  r="2"/><circle cx="2"
                                                                                                 cy="18"
                                                                                                 r="2"/></svg>
               </span>

            <div class="tile-nav"
                 id="tile-nav-<?php echo esc_attr( $tile->handle ); ?>" onmouseleave="closeTileNav('<?php echo esc_attr( $tile->handle ); ?>')">
                <div class="tile-move-left"
                     onclick="dt_dashboard.moveBack('<?php echo esc_attr( $tile->handle ); ?>')">
                    <i class="fi-arrow-left"></i> <?php esc_html_e( "Back", 'disciple-tools-dashboard' ); ?>
                </div>
                <div class="tile-move-right"
                     onclick="dt_dashboard.moveForward('<?php echo esc_attr( $tile->handle ); ?>')">
                    <i class="fi-arrow-right"></i> <?php esc_html_e( "Forward", 'disciple-tools-dashboard' ); ?>
                </div>
                <div class="tile-remove"
                     onclick="dt_dashboard.remove('<?php echo esc_attr( $tile->handle ); ?>')">
                    <i class="fi-x"></i> <?php esc_html_e( "Remove", 'disciple-tools-dashboard' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
