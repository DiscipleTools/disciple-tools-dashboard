<?php

class DT_Dashboard_Plugin_Update_Needed implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Update Needed';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/update-needed.php');
    }
}
