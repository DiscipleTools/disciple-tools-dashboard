<?php

class DT_Dashboard_Plugin_Tasks implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Tasks';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/tasks.php');
    }
}
