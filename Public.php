<?php

if (!function_exists('wp_content_translator_is_language_set')) {
    function wp_content_translator_is_language_set() : bool
    {
        return \ContentTranslator\Switcher::isLanguageSet();
    }
}

if (!function_exists('wp_content_translator_current_language')) {
    function wp_content_translator_current_language() : \stdClass
    {
        return \ContentTranslator\Switcher::$currentLanguage;
    }
}
