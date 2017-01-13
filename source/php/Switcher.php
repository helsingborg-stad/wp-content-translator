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
            add_filter('locale', array($this, 'swithcLocale'));
        }
    }

    /**
     * Finds and switches WP locale to the current language (not in admin)
     * Downloads missing language files as well
     * @param  string $lang Lang code
     * @return string       New lang code
     */
    public function swithcLocale(string $lang) : string
    {
        if (is_admin()) {
            return $lang;
        }

        if ($switchToLang = self::identifyLocale()) {
            return $switchToLang;
        }

        return $lang;
    }

    /**
     * Identifies the real locale key for a specific language code
     * @param  string $code Language code
     * @return string|bool  Language code or false
     */
    public static function identifyLocale(string $code = null)
    {
        if (is_null($code)) {
            $current = self::$currentLanguage;
            $code = $current->code;
        }

        require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
        $translations = wp_get_available_translations();

        foreach ($translations as $key => $translation) {
            if (array_values($translation['iso'])[0] === $code) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Check get param and cookie for language
     * @return mixed
     */
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
        if (!is_admin() && !\ContentTranslator\Language::isActive($code)) {
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
