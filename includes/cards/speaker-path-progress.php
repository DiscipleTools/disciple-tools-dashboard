<?php


class DT_Dashboard_Plugin_Speaker_Path_Progress implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Speaker Path Progress';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/speaker-path-progress.php');
    }
}
