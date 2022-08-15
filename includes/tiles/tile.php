<?php

abstract class DT_Dashboard_Tile
{
    public $handle;
    public $label;
    public $span = 0;
    public $priority = 0;

    public function __construct( $handle, $label, $params = [] ) {
        if ( isset( $params['span'] ) ) {
            if ( ! is_numeric( $params['span'] ) ) {
                throw new Exception( 'Tile span must be numeric' );
            }

            if ( $params['span'] < 1 || $params['span'] > 4 ) {
                throw new Exception( 'Tile span must be between 1 and 4' );
            }

            $this->span = $params['span'];
        }

        if ( isset( $params['priority'] ) ) {
            $this->priority = $params['priority'];
        }

        $this->handle = $handle;
        $this->label = $label;
    }

    /**
     * Register any assets the tile needs or do anything else needed on registration.
     * @return mixed
     */
    public function setup() {

    }

    /**
     * Return the tile html.
     */
    abstract public function render();

    public function as_html() {
        ob_start();
        $tile = $this;
        include DT_Dashboard_Plugin::includes_dir() . 'template-parts/tile.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function to_array() {
        return [
            'handle' => $this->handle,
            'label' => $this->label,
            'span' => $this->span,
            'template' => $this->as_html()
        ];
    }

    public function to_json() {
        return json_encode( $this->to_array() );
    }
}
