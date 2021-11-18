<?php

/**
 * Service class for working with dashboard cards.
 * Class DT_Dashboard_Plugin_Cards
 */
class DT_Dashboard_Plugin_Cards
{
    const CARDS_FILTER = 'dt_dashboard_cards';
    const CARD_SORT_OPTION = 'dt_dashboard_card_sort';
    const CARD_VISIBLE_OPTION_PREFIX = 'dt_dashboard_card_';
    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    static $cards = [];

    /**
     * Get cards as an array
     * @return array
     */
    public function all()
    {
        return apply_filters( static::CARDS_FILTER, static::$cards );
    }

    /**
     * @param $handle
     * @return bool
     */
    public function is_card_visible($handle) {
        return get_option(static::CARD_VISIBLE_OPTION_PREFIX . $handle) !== '0';
    }

    /**
     * Get all hidden cards
     * @return array
     */
    public function hidden()
    {
        return array_filter($this->all(), function ($card, $handle) {
            return !$this->is_card_visible($handle);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get all shown cards
     * @return array
     */
    public function shown()
    {
        $cards = array_filter($this->all(), function ($card, $handle) {
            return $this->is_card_visible($handle);
        }, ARRAY_FILTER_USE_BOTH);
        $this->sort_cards($cards);
        return $cards;
    }

    /**
     * Find a card by handle.
     * @param $handle
     * @return mixed
     */
    public function find($handle)
    {
        return $this->all()[$handle];
    }

    /**
     * Register a card.
     *
     * @param $slug
     * @param DT_Dashboard_Card $card
     */
    public function register(DT_Dashboard_Card $card)
    {
        add_action( 'wp_enqueue_scripts', function() use ($card) {
            $this->setup_card($card);
        }, 999 );

        //Register the card
        static::$cards[$card->handle] = $card;
    }

    /**
     * Deregister a card.
     *
     * @param $slug
     * @param DT_Dashboard_Plugin_Card $card
     */
    public function deregister($handle)
    {
        unset(static::$cards[$handle]);
    }

    /**
     * Set a cards's visibility option.
     * @param $handle
     * @param $visibility
     */
    public function set_card_visibility($handle, $visibility)
    {
        update_option(static::CARD_VISIBLE_OPTION_PREFIX . $handle, $visibility ? '1' : '0');
    }

    /**
     * Hide a card
     * @param $handle
     * @param string $scope
     */
    public function hide($handle)
    {
        $this->set_card_visibility($handle, false);
    }

    /**
     * Toggle a cards visibility option
     * @param $handle
     */
    public function toggle($handle)
    {
        $this->set_card_visibility($handle, !$this->shown());
    }

    /**
     * Show a card
     * @param $handle
     * @param string $scope
     */
    public function show($handle)
    {
        $this->set_card_visibility($handle, true);
    }

    /**
     * Sort cards
     * @param $handle
     * @param string $scope
     */
    public function sort($handles)
    {
        update_option(static::CARD_SORT_OPTION, json_encode($handles));
    }

    /**
     * Setup a card. First check if there is a custom setup action.
     * @param $slug
     * @param $card
     */
    public function setup_card($card)
    {
        $setup_action = 'dt_dashboard_setup_card_' . $card->handle;
        do_action($setup_action, $card);
        if (!has_action($setup_action)) {
            $card->setup();
        }
    }

    /**
     * Get a card's sort option
     * @return mixed
     */
    protected function get_card_sort_option()
    {
        return get_option(static::CARD_SORT_OPTION);
    }

    /**
     * Get the sort order of all cards
     * @return mixed
     */
    protected function get_sort()
    {
        $sort = $this->get_card_sort_option();
        if (!$sort) {
            $sort = json_encode([]);
        }
        return json_decode($sort, true);
    }

    /**
     * Sort the cards by their stored sort order
     * @param $cards
     */
    protected function sort_cards(&$cards)
    {
        $sort = $this->get_sort();
        uasort($cards, function ($a, $b) use ($sort, $cards) {
            $handle_a = array_search($a, $cards);
            $handle_b = array_search($b, $cards);
            $sort_a = array_search($handle_a, $sort);
            $sort_b = array_search($handle_b, $sort);
            if (!$sort_a) {
                $sort_a = $a->priority;
            }
            if (!$sort_b) {
                $sort_b = $b->priority;
            }

            return ($sort_a + 1 < $sort_b + 1) ? -1 : 1;
        });
    }
}
