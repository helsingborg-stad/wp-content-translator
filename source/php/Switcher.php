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
    }
}
