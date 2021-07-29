<?php

class DT_Dashboard_Callback_Card extends DT_Dashboard_Card
{
    private $setupCallback;
    private $renderCallback;

    public function __construct($handle, $label, $params = [])
    {
        $this->setupCallback = $params['setup'];
        $this->renderCallback = $params['render'];
        parent::__construct($handle, $label, $params);
    }

    /**
     * Register any assets the card needs or do anything else needed on registration.
     * @return mixed
     */
    public function setup() {
        $callback = $this->setupCallback;
        if ($callback) {
            $callback();
        }
    }

    /**
     * Render the card
     */
    public function render() {
        $callback = $this->renderCallback;
        if ($callback) {
            $callback();
        }
    }
}
