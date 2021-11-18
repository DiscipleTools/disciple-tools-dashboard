<?php

abstract class DT_Dashboard_Card
{
    public $handle;
    public $label;
    public $span = 1;
    public $priority = 0;

    public function __construct($handle, $label, $params = [])
    {
        if (isset($params['span'])) {
            if (! is_numeric($params['span'])) {
                throw new Exception('Card span must be numeric');
            }

            if ($params['span'] < 1 || $params['span'] > 4) {
                throw new Exception('Card span must be between 1 and 4');
            }

            $this->span = $params['span'];
        }

        if (isset($params['priority'])) {
            $this->priority = $params['priority'];
        }

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
        $card = $this;
        include DT_Dashboard_Plugin::includes_dir() . 'template-parts/card.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function toArray() {
        return [
            'handle' => $this->handle,
            'label' => $this->label,
            'span' => $this->span,
            'template' => $this->asHtml()
        ];
    }

    public function toJson() {
        return json_encode($this->toArray());
    }
}
