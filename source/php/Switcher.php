<?php

namespace ContentTranslator;

class Switcher
{
    public static $cookieKey = 'wp_content_translator_language';
    public static $currentLanguage;

    public function __construct()
    {
        add_action('admin_init', array($this, 'switchToDefaultWhenCreating'));

        if ($lang = $this->getRequestedLang()) {
            $this->switchToLanguage($lang);
            add_filter('locale', array($this, 'switchLocale'));
        } else {
            $this->switchToDefaultLang();
        }
    }

    public function switchToDefaultWhenCreating()
    {
        if (basename($_SERVER["SCRIPT_FILENAME"]) !== 'post-new.php' || \ContentTranslator\Language::isDefault(self::$currentLanguage->code)) {
            return;
        }

        $this->switchToDefaultLang();
        add_action('admin_notices', function () {
            echo '<div class="notice notice-info is-dismissible"><p>' . __('You can\'t create a new posts in a translation. Switched to the default language for you.') . '</p></div>';
        });
        return;
    }

    /**
     * Finds and switches WP locale to the current language (not in admin)
     * Downloads missing language files as well
     * @param  string $lang Lang code
     * @return string       New lang code
     */
    public function switchLocale(string $lang) : string
    {
        // Only run this filter once
        remove_filter('locale', array($this, 'switchLocale'));

        if (is_admin()) {
            return $lang;
        }

        if (isset(self::$currentLanguage->code)) {
            return self::$currentLanguage->code;
        }

        return $lang;
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
        if (isset($_COOKE[self::$cookieKey])) {
            unset($_COOKE[self::$cookieKey]);
            setcookie(self::$cookieKey, null, -1, '/', COOKIE_DOMAIN);
        }

        $this->switchToLanguage(get_locale());

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
