<?php

namespace ContentTranslator\Entity;

abstract class Translate
{
    protected $lang;
    protected $db;
    protected $configuration;

    abstract public static function install(string $language) : bool;
    abstract public static function isInstalled(string $language) : bool;
    abstract public static function uninstall(string $language) : bool;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;

        $this->setupConfiguration();

        if (isset(\ContentTranslator\Switcher::$currentLanguage->code)) {
            $this->lang = \ContentTranslator\Switcher::$currentLanguage->code;
        }
    }

    public function setupConfiguration()
    {
        $this->configuration = (object) array(
            'general' => include(WPCONTENTTRANSLATOR_CONFIG_PATH . 'General.php'),
            'post' => include(WPCONTENTTRANSLATOR_CONFIG_PATH . 'Post.php'),
            'meta' => include(WPCONTENTTRANSLATOR_CONFIG_PATH . 'Meta.php'),
            'option' => include(WPCONTENTTRANSLATOR_CONFIG_PATH . 'Option.php'),
            'siteoption' => include(WPCONTENTTRANSLATOR_CONFIG_PATH . 'SiteOption.php'),
            'usermeta' => include(WPCONTENTTRANSLATOR_CONFIG_PATH . 'UserMeta.php'),
            'comment' => include(WPCONTENTTRANSLATOR_CONFIG_PATH . 'Comment.php'),
        );
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

        return $key . $this->configuration->general->translate_delimeter . $this->lang;
    }

    /**
     * Check if key is a langual option
     * @param  string  $key Option key
     * @return boolean
     */
    protected function isLangual($key)
    {
        return substr($key, -strlen($this->configuration->general->translate_delimeter . $this->lang)) == $this->configuration->general->translate_delimeter . $this->lang ? true : false;
    }
}
