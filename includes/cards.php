<?php


class DT_Dashboard_Plugin_Cards
{
    static $cards = [];

    /**
     * Get cards as an array
     * @return array
     */
    public function all()
    {
        $cards = static::$cards;
        $this->sort_cards($cards);
        return static::$cards;
    }

    private function get_sort()
    {
        return json_decode(get_option('dt_dashboard_card_sort', json_encode([])), true);
    }

    private function sort_cards(&$cards)
    {
        $sort = $this->get_sort();
        usort($cards, function ($a, $b) use ($sort, $cards) {
            $handle_a = array_search($a, $cards);
            $handle_b = array_search($b, $cards);
            $sort_a = array_search($handle_a, $sort);
            $sort_b = array_search($handle_b, $sort);

            if ($sort_a == $sort_b || $sort_a && !$sort_b) {
                return 0;
            }

            if (!$sort_a && $sort_b) {
                return 1;
            }

            return ($sort_a < $sort_b) ? -1 : 1;
        });
    }

    public function hidden()
    {
        return array_filter($this->all(), function ($card, $handle) {
            return !get_option('dt_dashboard_card_' . $handle);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function shown()
    {
        return array_filter($this->all(), function ($card, $handle) {
            return get_option('dt_dashboard_card_' . $handle);
        }, ARRAY_FILTER_USE_BOTH);
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
     * Setup a card. First check if there is a custom setup action.
     * @param $slug
     * @param $card
     */
    private function setup_card($handle, $card)
    {
        $setup_action = 'dt_dashboard_setup_card_' . $handle;
        do_action($setup_action, $card);
        if (!has_action($setup_action)) {
            $card->setup();
        }
    }

    /**
     * Register a card.
     *
     * @param $slug
     * @param DT_Dashboard_Plugin_Card $card
     */
    public function register($handle, DT_Dashboard_Plugin_Card $card)
    {
        $this->setup_card($handle, $card);

        //Register the card
        self::$cards[$handle] = $card;
    }

    /**
     * Deregister a card.
     *
     * @param $slug
     * @param DT_Dashboard_Plugin_Card $card
     */
    public function deregister($handle)
    {
        unset(self::$cards[$handle]);
    }

    /**
     * Hide a card
     * @param $handle
     * @param string $scope
     */
    public function hide($handle)
    {
        $option = 'dt_dashboard_card_' . $handle;
        update_option($option, false);

    }

    /**
     * Show a card
     * @param $handle
     * @param string $scope
     */
    public function show($handle)
    {
        $option = 'dt_dashboard_card_' . $handle;
        update_option($option, true);
    }

    /**
     * Sort cards
     * @param $handle
     * @param string $scope
     */
    public function sort($handles)
    {
        $option = 'dt_dashboard_card_sort';
        update_option($option, json_encode($handles));
    }
}
