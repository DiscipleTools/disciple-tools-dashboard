<?php


interface DT_Dashboard_Plugin_Card
{
    /**
     * The card label.
     * @return string
     */
    public function label();

    /**
     * Register any assets the card needs or do anything else needed on registration.
     * @return mixed
     */
    public function setup();

    /**
     * Return the card html.
     * @return string
     */
    public function render();
}
