<?php


class DT_Dashboard_Plugin_Personal_Benchmarks implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Personal Benchmarks';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/personal-benchmarks.php');
    }
}
