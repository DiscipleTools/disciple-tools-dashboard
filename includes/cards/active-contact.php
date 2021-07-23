<?php


class DT_Dashboard_Plugin_Active_Contact implements DT_Dashboard_Plugin_Card
{
    public function label()
    {
        return 'Active Contacts';
    }

    public function setup()
    {
        // TODO: Implement register() method.
    }

    public function render()
    {
        include (DT_Dashboard_Plugin::includes_dir() . 'template-parts/cards/active-contact.php');
    }
}
