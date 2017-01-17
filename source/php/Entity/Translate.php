<?php

namespace ContentTranslator\Entity;

abstract class Translate
{
    protected $lang;
    protected $db;

    abstract public static function install(string $language) : bool;
    abstract public static function isInstalled(string $language) : bool;
    abstract public static function uninstall(string $language) : bool;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;

        if (isset(\ContentTranslator\Switcher::$currentLanguage->code)) {
            $this->lang = \ContentTranslator\Switcher::$currentLanguage->code;
        }
    }

    /**
     * Creates a language specific meta/options key
     * @param  string $key The meta/option key
     * @return string      Langual meta/option key
     */
    protected function createLangualKey(string $key) : string
    {
        if ($this->isLangual($key)) {
            return $key;
        }

        return $key . TRANSLATE_DELIMITER . $this->lang;
    }

    /**
     * Check if key is a langual option
     * @param  string  $key Option key
     * @return boolean
     */
    protected function isLangual($key)
    {
        return substr($key, -strlen(TRANSLATE_DELIMITER . $this->lang)) == TRANSLATE_DELIMITER . $this->lang ? true : false;
    }
}
