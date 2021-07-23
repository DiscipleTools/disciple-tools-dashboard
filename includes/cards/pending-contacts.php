<?php


class DT_Dashboard_Plugin_Pending_Contacts implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Pending Contacts';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/pending-contacts.php');
    }
}
