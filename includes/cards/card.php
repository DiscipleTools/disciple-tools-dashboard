<?php


class DT_Dashboard_Plugin_Card
{
    public $handle;
    public $label;

    public function __construct($handle, $label)
    {
        $this->handle = $handle;
        $this->label = $label;
    }

    /**
     * Register any assets the card needs or do anything else needed on registration.
     * @return mixed
     */
    public function setup() {
        //Do anything needed on setup.
        //Can be used by extended custom cards.
    }

    /**
     * Return the card html.
     */
    public function render() {
        $handle = $this->handle;
        $label = $this->label;
        $card = $this;
        $template_file = str_replace('_', '-', str_replace('DT_Dashboard_Plugin_', '', $handle)) . '.php';
        include (DT_Dashboard_Plugin::includes_dir() . "template-parts/cards/" . $template_file);
    }
}
