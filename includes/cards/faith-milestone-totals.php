<?php


class DT_Dashboard_Plugin_Faith_Milestone_Totals implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Faith Milestone Totals';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/faith-milestone-totals.php');
    }
}
