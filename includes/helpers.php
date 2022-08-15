<?php

function dt_dashboard_register_tile( $handle, $label, $setup, $render, $priority = 0 ) {
    DT_Dashboard_Plugin_Tiles::instance()->register(
        new DT_Dashboard_Callback_Tile($handle, $label, [
            'setup' => $setup,
            'render' => $render,
            'priority' => $priority
        ])
    );
};
