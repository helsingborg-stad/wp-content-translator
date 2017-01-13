<?php

namespace ContentTranslator;

class Switcher
{
    public static $cookieKey = 'wp_content_translator_language';
    public static $currentLanguage;

    public function __construct()
    {
        if ($lang = $this->getRequestedLang()) {
            $this->switchToLanguage($lang);
        }
    }

    public function getRequestedLang()
    {
        if (isset($_GET['lang']) && !empty($_GET['lang'])) {
            return $_GET['lang'];
        }

        if (isset($_COOKIE[self::$cookieKey]) && !empty($_COOKIE[self::$cookieKey])) {
            return $_COOKIE[self::$cookieKey];
        }

        return false;
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
            setcookie(self::$cookieKey, $code, time() + (3600 * 24 * 30), '/', COOKIE_DOMAIN);
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
