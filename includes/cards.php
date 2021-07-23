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
        return self::$cards;
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
    private function setup_card($handle, $card) {
        $setup_action = 'dt_dashboard_setup_card_' . $handle;
        do_action( $setup_action, $card);
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
}
