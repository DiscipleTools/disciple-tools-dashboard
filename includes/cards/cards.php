<?php


class DT_Dashboard_Plugin_Cards
{
    const CARD_SORT_OPTION = 'dt_dashboard_card_sort';
    const CARD_VISIBLE_OPTION_PREFIX = 'dt_dashboard_card_';

    static $cards = [];

    /**
     * Get cards as an array
     * @return array
     */
    public function all()
    {
        return static::$cards;
    }


    public function is_card_visible($handle) {
        return get_option(static::CARD_VISIBLE_OPTION_PREFIX . $handle) !== '0';
    }


    public function hidden()
    {
        return array_filter($this->all(), function ($card, $handle) {
            return !$this->is_card_visible($handle);
        }, ARRAY_FILTER_USE_BOTH);
    }

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
     * @param DT_Dashboard_Plugin_Card $card
     */
    public function register(DT_Dashboard_Plugin_Card $card)
    {
        $this->setup_card($card);

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

    public function set_card_visibility($handle, $visibility) {
        update_option(static::CARD_VISIBLE_OPTION_PREFIX . $handle, $visibility ? 1 : 0);
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
    protected function setup_card($card)
    {
        $setup_action = 'dt_dashboard_setup_card_' . $card->handle;
        do_action($setup_action, $card);
        if (!has_action($setup_action)) {
            $card->setup();
        }
    }

    protected function get_card_sort_option() {
        return get_option(static::CARD_SORT_OPTION);
    }

    protected function get_sort()
    {
        $sort = $this->get_card_sort_option();
        if (!$sort) {
            $sort = json_encode([]);
        }
        return json_decode($sort, true);
    }

    protected function sort_cards(&$cards)
    {
        $sort = $this->get_sort();
        uasort($cards, function ($a, $b) use ($sort, $cards) {
            $handle_a = array_search($a, $cards);
            $handle_b = array_search($b, $cards);
            $sort_a = array_search($handle_a, $sort);
            $sort_b = array_search($handle_b, $sort);
            if (!$sort_a) {
                $sort_a = -1;
            }
            if (!$sort_b) {
                $sort_b = -1;
            }

            return ($sort_a + 1 < $sort_b + 1) ? -1 : 1;
        });
    }
}
