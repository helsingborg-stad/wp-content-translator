<?php

namespace ContentTranslator;

class User Extends Meta
{

    protected $lang;
    protected $db;

    public function __construct()
    {
        if (WCT_TRANSLATE_USER && \ContentTranslator\Switcher::isLanguageSet() && !\ContentTranslator\Language::isDefault()) {
            global $wpdb;

            $this->lang = \ContentTranslator\Switcher::$currentLanguage->code;
            $this->db   = $wpdb;

            add_filter('get_user_metadata', array($this, 'get'), 1, 4);
            add_filter('update_user_metadata', array($this, 'save'), 1, 4);
            add_filter('add_user_metadata', array($this, 'save'), 1, 4);
        }
    }
}
