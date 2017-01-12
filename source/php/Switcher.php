<?php

namespace ContentTranslator;

class Switcher
{
    public static $currentLanguage;

    public function __construct()
    {
        $this->switchToLanguage('en');
    }

    public function switchToLanguage(string $code)
    {
        if (!\ContentTranslator\Language::isActive($code)) {
            $lang = \ContentTranslator\Language::find($code);
            throw new \Exception("WP Content Translator: Can't switch language to '" . $lang->name . "' because it's not activated.", 1);
        }

        self::$currentLanguage = \ContentTranslator\Language::find($code);

        add_action('init', array($this, 'wpdbSwitch'));
        //add_filter('query', array($this, 'filterQuery'));
    }

    public function wpdbSwitch()
    {
        global $wpdb;

        $wpdb->posts .= '_' . self::$currentLanguage->code;
        $wpdb->postmeta .= '_' . self::$currentLanguage->code;
    }

    public function filterQuery($sql)
    {
        var_dump($sql);
        exit;
    }
}
