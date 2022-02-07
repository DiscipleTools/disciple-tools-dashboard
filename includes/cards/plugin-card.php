<?php


class DT_Dashboard_Plugin_Card extends DT_Dashboard_Card
{
    private $template_folder = '';

    public function __construct( $handle, $label, $span = 1) {
        $this->template_folder = strtolower( str_replace( '_', '-', str_replace( 'DT_Dashboard_Plugin_', '', $handle ) ) );
        parent::__construct( $handle, $label, $span );
    }

    /**
     * Register any assets the card needs or do anything else needed on registration.
     * @return mixed
     */
    public function setup() {
        $script = "includes/cards/" . $this->template_folder . "/scripts.js";

        if (file_exists( DT_Dashboard_Plugin::dir() . $script )) {
            wp_enqueue_script( $this->handle, DT_Dashboard_Plugin::path() . $script, [
                'dt-dashboard-plugin',
                'jquery',
                'jquery-ui',
                'lodash',
                'amcharts-core',
                'amcharts-charts',
                'amcharts-animated',
                'moment'
            ], filemtime( DT_Dashboard_Plugin::dir() . $script ), true);
        }
    }

    /**
     * Return the card html.
     */
    public function render() {
        $handle = $this->handle;
        $label = $this->label;
        $card = $this;
        include( DT_Dashboard_Plugin::includes_dir() . "cards/" . $this->template_folder . "/template.php" );
    }
}
