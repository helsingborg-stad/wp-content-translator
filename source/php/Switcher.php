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
            add_filter('locale', array($this, 'switchLocale'));
        }
    }

    /**
     * Finds and switches WP locale to the current language (not in admin)
     * Downloads missing language files as well
     * @param  string $lang Lang code
     * @return string       New lang code
     */
    public function switchLocale(string $lang) : string
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
            return $this->switchToDefaultLang();
        }

        self::$currentLanguage = \ContentTranslator\Language::find($code);

        if (self::$currentLanguage !== false) {
            setcookie(self::$cookieKey, $code, time() + (3600 * 24 * 30), '/', COOKIE_DOMAIN);
        }

        return true;
    }

    /**
     * Switches to the default WordPress language
     * @return bool
     */
    public function switchToDefaultLang() : bool
    {
        unset($_COOKE[self::$cookieKey]);
        setcookie(self::$cookieKey, null, -1, '/', COOKIE_DOMAIN);

        return true;
    }

    /**
     * Renders selector element
     * @param  array|null   $languages The languages to use in the selector
     * @param  string|null  $wrapper   The template for the wrapper
     * @param  string|null  $element   The template for each language item
     * @param  bool|boolean $echo      Echo or not
     * @return void|string
     */
    public static function selector(string $wrapper = null, string $element = null, bool $echo = true, array $languages = null)
    {
        if (is_null($languages)) {
            $languages = wp_content_translator_languages('active');
        }

        if (is_null($wrapper)) {
            $wrapper = '<select>{{ languages }}</select>';
        }

        if (is_null($element)) {
            $element = '<option onclick="location.href=\'{{ url }}\';" data-langcode="{{ code }}">{{ nativeName }}</option>';
        }

        $elements = array();
        foreach ($languages as $lang) {
            $thisElement = $element;
            $thisElement = preg_replace_callback('/\{\{\s?(\w+)\s?\}\}/i', function ($matches) use ($lang) {
                if (empty($matches)) {
                    return '';
                }

                if ($matches[1] === 'isCurrent') {
                    if ($lang->isCurrent) {
                        return 'is-current';
                    }

                    return '';
                }

                if (!isset($lang->{$matches[1]})) {
                    return '';
                }

                return $lang->{$matches[1]};
            }, $thisElement);

            $elements[] = $thisElement;
        }

        $output = preg_replace_callback('/\{\{\s?languages\s?\}\}/i', function ($matches) use ($elements) {
            if (empty($matches)) {
                return '';
            }

            return implode("\n", $elements);
        }, $wrapper);

        if (!$echo) {
            return $output;
        }

        echo $output;
    }

    /**
     * Checks if language is set
     * Also available as public function: wp_content_translator_is_language_set()
     * @return boolean
     */
    public static function isLanguageSet() : bool
    {
        return (bool) isset(self::$currentLanguage) && !is_null(self::$currentLanguage) && !empty(self::$currentLanguage);
    }
}
