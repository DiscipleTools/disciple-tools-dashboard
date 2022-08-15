<?php

/**
 * Service class for working with dashboard tiles.
 * Class DT_Dashboard_Plugin_Tiles
 */
class DT_Dashboard_Plugin_Tiles {
    const CARDS_FILTER = 'dt_dashboard_tiles';
    const CARD_SORT_OPTION = 'dt_dashboard_tile_sort';
    const CARD_VISIBLE_OPTION_PREFIX = 'dt_dashboard_tile_';
    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static $tiles = [];

    /**
     * Get tiles as an array
     * @return array
     */
    public function all() {
        return apply_filters( static::CARDS_FILTER, static::$tiles );
    }

    /**
     * @param $handle
     * @return bool
     */
    public function is_tile_visible( $handle ) {
        return get_option( static::CARD_VISIBLE_OPTION_PREFIX . $handle ) !== '0';
    }

    /**
     * Get all hidden tiles
     * @return array
     */
    public function hidden() {
        return array_filter( $this->all(), function ( $tile, $handle ) {
            return !$this->is_tile_visible( $handle );
        }, ARRAY_FILTER_USE_BOTH );
    }

    /**
     * Get all shown tiles
     * @return array
     */
    public function shown() {
        $tiles = array_filter( $this->all(), function ( $tile, $handle ) {
            return $this->is_tile_visible( $handle );
        }, ARRAY_FILTER_USE_BOTH );
        $this->sort_tiles( $tiles );
        return $tiles;
    }

    /**
     * Find a tile by handle.
     * @param $handle
     * @return mixed
     */
    public function find( $handle ) {
        return $this->all()[ $handle ];
    }

    /**
     * Register a tile.
     *
     * @param DT_Dashboard_Tile $tile
     */
    public function register( DT_Dashboard_Tile $tile ) {
        add_action( 'wp_enqueue_scripts', function () use ( $tile ) {
            $this->setup_tile( $tile );
        }, 999 );

        //Register the tile
        static::$tiles[ $tile->handle ] = $tile;
    }

    /**
     * Deregister a tile.
     *
     * @param $handle
     */
    public function deregister( $handle ) {
        unset( static::$tiles[ $handle ] );
    }

    /**
     * Set a tiles's visibility option.
     * @param $handle
     * @param $visibility
     */
    public function set_tile_visibility( $handle, $visibility ) {
        update_option( static::CARD_VISIBLE_OPTION_PREFIX . $handle, $visibility ? '1' : '0' );
    }

    /**
     * Hide a tile
     * @param $handle
     */
    public function hide( $handle ) {
        $this->set_tile_visibility( $handle, false );
    }

    /**
     * Toggle a tiles visibility option
     * @param $handle
     */
    public function toggle( $handle ) {
        $this->set_tile_visibility( $handle, !$this->shown() );
    }

    /**
     * Show a tile
     * @param $handle
     */
    public function show( $handle ) {
        $this->set_tile_visibility( $handle, true );
    }

    /**
     * Sort tiles
     * @param $handle
     */
    public function sort( $handles ) {
        update_option( static::CARD_SORT_OPTION, json_encode( $handles ) );
    }

    /**
     * Setup a tile. First check if there is a custom setup action.
     * @param $slug
     * @param $tile
     */
    public function setup_tile( $tile ) {
        $setup_action = 'dt_dashboard_setup_tile_' . $tile->handle;
        do_action( $setup_action, $tile );
        if ( !has_action( $setup_action ) ) {
            $tile->setup();
        }
    }

    /**
     * Get a tile's sort option
     * @return mixed
     */
    protected function get_tile_sort_option() {
        return get_option( static::CARD_SORT_OPTION );
    }

    /**
     * Get the sort order of all tiles
     * @return mixed
     */
    protected function get_sort() {
        $sort = $this->get_tile_sort_option();
        if ( !$sort ) {
            $sort = json_encode( [] );
        }
        return json_decode( $sort, true );
    }

    /**
     * Sort the tiles by their stored sort order
     * @param $tiles
     */
    protected function sort_tiles( &$tiles ) {
        $sort = $this->get_sort();
        uasort( $tiles, function ( $a, $b ) use ( $sort, $tiles ) {
            $handle_a = array_search( $a, $tiles );
            $handle_b = array_search( $b, $tiles );
            $sort_a = array_search( $handle_a, $sort );
            $sort_b = array_search( $handle_b, $sort );

            if ( !is_numeric( $sort_a ) ) {
                $sort_a = $a->priority;
            }
            if ( !is_numeric( $sort_b ) ) {
                $sort_b = $b->priority;
            }

            return ( $sort_a + 1 < $sort_b + 1 ) ? -1 : 1;
        } );
    }
}
