<?php

namespace ContentTranslator\Entity;

abstract class Translate
{
    protected $lang;
    protected $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->lang = \ContentTranslator\Switcher::$currentLanguage->code;
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

    function install(string $language) {}
    function isInstalled(string $language) {}
    function uninstall(string $language) {}
}
