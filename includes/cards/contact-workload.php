<?php


class DT_Dashboard_Plugin_Contact_Workload implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Contact Workload';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/contact-workload.php');
    }
}
