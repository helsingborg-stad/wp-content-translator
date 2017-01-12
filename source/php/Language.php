<?php

namespace ContentTranslator;

class Language
{
    public static $all;

    public static $optionKey = array(
        'installed' => 'wp-content-translator-installed'
    );

    protected $db;

    private $code;
    private $language;
    private $tables;

    public function __construct($code)
    {
        global $wpdb;
        $this->db = $wpdb;

        $this->code = $code;
        $this->language = self::find($code);

        $this->tables = array(
            $this->db->posts => array(
                'name' => $this->db->posts . '_' . $this->code,
                'auto_increment' => 'ID'
            ),
            $this->db->postmeta => array(
                'name' => $this->db->postmeta . '_' . $this->code,
                'auto_increment' => 'meta_id'
            )
        );

        if (!$this->isInstalled()) {
            $this->install();
        }
    }

    /**
     * Installs the language if needed
     * @return boolean
     */
    public function install() : bool
    {
        foreach ($this->tables as $source => $target) {
            $this->duplicateTable($source, $target['name']);
        }

        $installed = get_option(self::$optionKey['installed'], array());
        $installed[] = $this->code;

        update_option('wp-content-translator-installed', $installed);

        return true;
    }

    /**
     * Duplicates a table
     * @param  string $source Name of table to duplicate
     * @param  string $target Table name to create
     * @return boolean
     */
    public function duplicateTable($source, $target) : bool
    {
        if (!$this->tableExist($source)) {
            throw new \Exception("Table '" . $source . "' does not exist.", 1);
        }

        if ($this->tableExist($target)) {
            throw new \Exception("Table '" . $target . "' already exist. You will have to manually (with caution) drop the table to continue.", 1);
        }

        // Find autoincrement column name
        $ai = $this->tables[$source]['auto_increment'];

        // Create sql
        $sql = "CREATE TABLE $target LIKE $source;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $this->db->query("ALTER TABLE $target CHANGE $ai $ai BIGINT(20) UNSIGNED NOT NULL");

        return true;
    }

    /**
     * Checks if a database table exists
     * @param  string $table Table name
     * @return boolean
     */
    public function tableExist($table) : bool
    {
        if ($this->db->get_var("SHOW TABLES LIKE '$table'") !== $table) {
            return false;
        }

        return true;
    }

    /**
     * Check if tables is installed
     * @return boolean
     */
    public function isInstalled() : bool
    {
        foreach ($this->tables as $key => $table) {
            if ($this->db->get_var("SHOW TABLES LIKE '" . $table['name'] . "'") !== $table['name']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the default language (from wp settings)
     * @return stdClass Language
     */
    public static function default() : \stdClass
    {
        $locale = get_locale();
        $locale = explode('_', $locale);
        $identifier = $locale[0];

        return self::find($identifier);
    }

    /**
     * Gets all languages
     * @return array Languages
     */
    public static function all()
    {
        if (isset(self::$all) && !empty(self::$all)) {
            return self::$all;
        }

        $json = file_get_contents(WPCONTENTTRANSLATOR_LANGUAGES_JSON_PATH);
        self::$all = json_decode($json);

        return self::$all;
    }

    public static function find($key) : \stdClass
    {
        $all = self::all();

        if (isset($all->$key)) {
            return $all->$key;
        }

        return new \stdClass();
    }

    /**
     * Get languages in use (both active and inactive)
     * @return array Languages
     */
    public static function installed() : array
    {
        $keys = get_option(\ContentTranslator\Language::$optionKey['installed']);
        $installed = array();

        foreach ($keys as $key) {
            $search = self::find($key);
            if ($search) {
                $installed[$key] = $search;
            }
        }

        return $installed;
    }

    /**
     * Get unused languages
     * @return array
     */
    public static function uninstalled() : array
    {
        $uninstalled = array_diff_key((array)self::all(), self::installed());
        return $uninstalled;
    }

    /**
     * Get active languages in use
     * @return array Languages
     */
    public static function active() : array
    {
        return (array)self::all();
    }
}
