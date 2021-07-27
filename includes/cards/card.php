<?php

abstract class DT_Dashboard_Card
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

    }

    /**
     * Return the card html.
     */
    abstract public function render();

    public function asHtml() {
        ob_start();
        $this->render();
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function toArray() {
        return [
            'handle' => $this->handle,
            'label' => $this->label,
            'template' => $this->asHtml()
        ];
    }

    public function toJson() {
        return json_encode($this->toArray());
    }
}
