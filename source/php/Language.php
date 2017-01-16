<?php

namespace ContentTranslator;

class Language
{
    public static $all;

    public static $optionKey = array(
        'installed' => 'wp-content-translator-installed',
        'active' => 'wp-content-translator-active'
    );

    protected $db;

    private $code;
    private $language;
    private $tables;

    public function __construct(string $code, bool $install = true)
    {
        global $wpdb;
        $this->db = $wpdb;

        $this->code = $code;
        $this->language = self::find($code);

        // Tables to create
        $this->tables = array(
            $this->db->posts => array(
                'name' => $this->db->posts . '_' . $this->code,
                'auto_increment' => 'ID'
            )
        );

        // Should we install (ie create tables and such) the language
        if ($install && !$this->isInstalled()) {
            $this->install();
        }
    }

    /**
     * Get table names for current language
     * @param  string $table Table to get (posts|postmeta)
     * @param  string $lang  Language code
     * @return array         Table
     */
    public static function getTable(string $table = null, string $lang = null) : array
    {
        $suffix = '';
        if (!self::isDefault() && \ContentTranslator\Switcher::isLanguageSet()) {
            $suffix = '_' . \ContentTranslator\Switcher::$currentLanguage->code;
        }

        $tables = array(
            'posts' => array(
                'name' => \ContentTranslator\App::$defaultWpdbTables['posts'] . $suffix,
                'auto_increment' => 'ID'
            )
        );

        if (is_null($table)) {
            return $tables;
        }

        return isset($tables[$table]) ? $tables[$table] : array();
    }

    /**
     * Installs the language if needed
     * @return boolean
     */
    public function install() : bool
    {
        do_action('wp-content-translator/before_install_language', $this->code, $this);

        foreach ($this->tables as $source => $target) {
            $this->duplicateTable($source, $target['name']);
        }

        $download = apply_filters('wp-content-translator/should_download_wp_translation_when_installing', true, $this->code, $this);
        if ($download && !in_array(\ContentTranslator\Switcher::identifyLocale($this->code), get_available_languages())) {
            $download = wp_download_language_pack(\ContentTranslator\Switcher::identifyLocale($this->code));
        }

        $installed = get_option(self::$optionKey['installed'], array());
        $installed[] = $this->code;

        update_option(self::$optionKey['installed'], $installed);

        do_action('wp-content-translator/after_install_language', $this->code, $this);

        return true;
    }

    public function uninstall() : bool
    {
        do_action('wp-content-translator/before_uninstall_language', $this->code, $this);

        if (apply_filters('wp-content-translator/should_drop_table_when_uninstalling_language', true)) {
            foreach ($this->tables as $source => $target) {
                $this->dropTable($target['name']);
            }
        }

        // Remove from activated
        $active = get_option(self::$optionKey['active'], array());
        if (array_search($this->code, $active) !== false) {
            $index = array_search($this->code, $active);
            unset($active[$index]);
        }

        update_option(self::$optionKey['active'], $active);

        // Remove from installed
        $installed = get_option(self::$optionKey['installed'], array());
        if (array_search($this->code, $installed) !== false) {
            $index = array_search($this->code, $installed);
            unset($installed[$index]);
        }

        update_option(self::$optionKey['installed'], $installed);

        do_action('wp-content-translator/after_uninstall_language', $this->code, $this);

        wp_redirect(apply_filters('wp-content-translator/redirect_after_uninstall_language', $_SERVER['HTTP_REFERER'], $this->code, $this));
        exit;
    }

    /**
     * Duplicates a table
     * @param  string $source Name of table to duplicate
     * @param  string $target Table name to create
     * @return boolean
     */
    public function duplicateTable(string $source, string $target) : bool
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
     * Drop a table
     * Note: Can only drop tables created by this plugin for saftey reasons
     * @param  string $table Table name
     * @return bool
     */
    public function dropTable(string $table) : bool
    {
        $droppable = false;
        foreach ($this->tables as $droppableTable) {
            if ($droppableTable['name'] === $table) {
                $droppable = true;
                break;
            }
        }

        if (!$droppable) {
            throw new \Exception("Ooopsidopsi. Can't do it.", 1);
        }

        global $wpdb;
        $wpdb->query("DROP TABLE $table");

        return true;
    }

    /**
     * Checks if a database table exists
     * @param  string $table Table name
     * @return boolean
     */
    public function tableExist(string $table) : bool
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
        return self::find($locale);
    }

    /**
     * Check if current language or given langauge is the default language
     * @param  string|null $code Langauge code
     * @return boolean
     */
    public static function isDefault(string $code = null)
    {
        if (is_null($code)) {
            $code = \ContentTranslator\Switcher::$currentLanguage->code;
        }

        $default = self::default();
        return $default->code === $code;
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

        $languages = array();

        require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
        $translations = json_decode(json_encode(wp_get_available_translations()));

        foreach ($translations as $key => $translation) {
            $languages[$key] = array(
                'code' => $translation->language,
                'name' => self::defaultLanguage($translation->english_name),
                'nativeName' => $translation->native_name
            );
        }

        uasort($languages, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $languages = json_decode(json_encode($languages));

        self::$all = $languages;
        return self::$all;
    }

    /**
     * Find a specific language by language code
     * @param  string $key Language code
     * @return stdClass    The language's info
     */
    public static function find(string $key) : \stdClass
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
    public static function installed($includeDefault = true) : array
    {
        $keys = get_option(\ContentTranslator\Language::$optionKey['installed'], array());
        $installed = array();

        if ($includeDefault) {
            $default = self::default();
            $installed[$default->code] = $default;
        }

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
        $defaultLang = self::default();

        $uninstalled = array_diff_key((array)self::all(), self::installed());
        if (isset($uninstalled[$defaultLang->code])) {
            unset($uninstalled[$defaultLang->code]);
        }

        return $uninstalled;
    }

    /**
     * Get active languages in use
     * @return array Languages
     */
    public static function active() : array
    {
        $keys = get_option(\ContentTranslator\Language::$optionKey['active'], array());
        $active = array();

        // Add default lang to active
        $default = self::default();
        $active[$default->code] = $default;

        foreach ($keys as $key) {
            $search = self::find($key);

            if ($search) {
                $active[$key] = $search;
            }
        }

        return $active;
    }

    /**
     * Checks if a language is activated
     * @param  string  $code Langage code
     * @return boolean
     */
    public static function isActive(string $code) : bool
    {
        $active = self::active();

        if (isset($active[$code])) {
            return true;
        }

        return false;
    }

    /**
     * Gives unnamed langages a name.
     * @param  string  $lang Langage name
     * @return string
     */
    public static function defaultLanguage(string $lang) : string
    {
        if(empty($lang)) {
            $lang = __("Undefined Language", 'wp-content-translator');
        }
        return $lang;
    }

}
