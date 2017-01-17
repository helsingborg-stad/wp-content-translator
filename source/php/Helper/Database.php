<?php


namespace ContentTranslator\Helper;

class Database
{
    private static $db;

    public function __construct()
    {
        global $wpdb;
        self::$db = $wpdb;
    }

    /**
     * Gets all column names of a table
     * @param  string $table Table name
     * @return array
     */
    public static function getTableColumns(string $table) : array
    {
        $columns = array();

        foreach ((array) self::$db->get_col("DESC " . self::$db->prefix . $table, 0) as $column_name) {
            $columns[] = $column_name;
        }

        return (array) $columns;
    }

    /**
     * Duplicates a table
     * @param  string $source Name of table to duplicate
     * @param  string $target Table name to create
     * @return boolean
     */
    public static function duplicateTable(string $source, string $target) : bool
    {
        if (!self::tableExist($source)) {
            throw new \Exception("Can't duplicate table since the table '" . $source . "' does not exist.", 1);
        }

        if (self::tableExist($target)) {
            throw new \Exception("Can't duplicate table since the table '" . $target . "' already exist. You will have to manually (with caution) drop the existing table to continue.", 1);
        }

        // Create sql
        $sql = "CREATE TABLE $target LIKE $source;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        return true;
    }

    /**
     * Drop a table
     * Note: Can only drop tables created by this plugin for saftey reasons
     * @param  string $table Table name
     * @return bool
     */
    public static function dropTable(string $table) : bool
    {
        /*
        if (!$droppable) {
            throw new \Exception("Ooopsidopsi. Can't do it.", 1);
        }
        */

        self::$db->query("DROP TABLE $table");
        return true;
    }

    /**
     * Checks if a database table exists
     * @param  string $table Table name
     * @return boolean
     */
    public static function tableExist(string $table) : bool
    {
        if (self::$db->get_var("SHOW TABLES LIKE '$table'") !== $table) {
            return false;
        }

        return true;
    }
}
