<?php

namespace ContentTranslate;

abstract class Translate
{

    private $db;

    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'setupDatabase'), 1);
        add_action('plugins_loaded', array($this, 'testFiler'), 2);
    }

    public function setupDatabase()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function testFilter()
    {
        //var_dump($this->db);
    }
}
