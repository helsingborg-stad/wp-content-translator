<?php

namespace ContentTranslator;

class Language
{
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
            'posts' => $this->db->posts . '_' . $this->code,
            'postmeta' => $this->db->postmeta . '_' . $this->code
        );

        if (!$this->isInstalled()) {
            var_dump("is not installed");
        }

        var_dump("is installed");

        exit;
    }

    /**
     * Check if tables is installed
     * @return boolean
     */
    public function isInstalled() : bool
    {
        foreach ($this->tables as $key => $table) {
            if ($this->db->get_var("SHOW TABLES LIKE '$table'") !== $table) {
                return false;
            }
        }

        return true;
    }

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
        $json = file_get_contents(WPCONTENTTRANSLATOR_LANGUAGES_JSON_PATH);
        return json_decode($json);
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
    public static function used() : array
    {
        return self::all();
    }

    /**
     * Get unused languages
     * @return array
     */
    public static function unused() : array
    {
        return self::all();
    }

    /**
     * Get active languages in use
     * @return array Languages
     */
    public static function active() : array
    {
        return self::all();
    }
}
