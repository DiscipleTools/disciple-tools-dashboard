<?php


class DT_Dashboard_Plugin_Tile extends DT_Dashboard_Tile
{
    private $template_folder = '';

    public function __construct( $handle, $label, $span = 1 ) {
        $this->template_folder = strtolower( str_replace( '_', '-', str_replace( 'DT_Dashboard_Plugin_', '', $handle ) ) );
        parent::__construct( $handle, $label, $span );
    }

    /**
     * Register any assets the tile needs or do anything else needed on registration.
     * @return mixed
     */
    public function setup() {
        $script = 'includes/tiles/' . $this->template_folder . '/scripts.js';
        $style = 'includes/tiles/' . $this->template_folder . '/style.css';

        if ( file_exists( DT_Dashboard_Plugin::dir() . $script ) ) {
            wp_enqueue_script( $this->handle, DT_Dashboard_Plugin::path() . $script, [
                'dt-dashboard-plugin',
                'dt-activity-logs',
                'jquery',
                'jquery-ui',
                'lodash',
                'amcharts-core',
                'amcharts-charts',
                'amcharts-animated',
                'moment'
            ], filemtime( DT_Dashboard_Plugin::dir() . $script ), true);
        }

        if ( file_exists( DT_Dashboard_Plugin::dir() . $style ) ) {
            wp_enqueue_style( $this->handle . '-style', DT_Dashboard_Plugin::path() . $style, [], filemtime( DT_Dashboard_Plugin::dir() . $style ) );
        }
    }

    /**
     * Return the tile html.
     */
    public function render() {
        $handle = $this->handle;
        $label = $this->label;
        $tile = $this;
        include( DT_Dashboard_Plugin::includes_dir() . 'tiles/' . $this->template_folder . '/template.php' );
    }
}
