<?php

namespace ContentTranslator;

class Switcher
{
    public static $currentLanguage;

    public function __construct()
    {
        if (isset($_GET['lang']) && !empty($_GET['lang'])) {
            $this->switchToLanguage($_GET['lang']);
        }
    }

    /**
     * Switches language and sets user cookie
     * @param  string $code Code of language to swtich to
     * @return bool
     */
    public function switchToLanguage(string $code) : bool
    {
        if (!\ContentTranslator\Language::isActive($code)) {
            $lang = \ContentTranslator\Language::find($code);
            throw new \Exception("WP Content Translator: Can't switch language to '" . $lang->name . "' because it's not activated.", 1);
        }

        self::$currentLanguage = \ContentTranslator\Language::find($code);

        if (self::$currentLanguage !== false) {
            setcookie('wp_content_translator_language', $code, MONTH_IN_SECONDS, '/', COOKIE_DOMAIN);
        }

        return true;
    }

    /**
     * Checks if language is set
     * Also available as public function: wp_content_translator_is_language_set()
     * @return boolean
     */
    public static function isLanguageSet()
    {
        return isset(self::$currentLanguage) && !is_null(self::$currentLanguage) && !empty(self::$currentLanguage);
    }
}
