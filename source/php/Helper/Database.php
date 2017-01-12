<?php


namespace ContentTranslator\Helper;

class Database
{

    protected $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function getTableColumns($table) : array
    {
        $columns = array();

        foreach ( (array) $this->db->get_col( "DESC " . $wpdb->prefix . $table , 0 ) as $column_name) {
            $columns[] = $column_name;
        }

        return (array) $columns;

    }
}
