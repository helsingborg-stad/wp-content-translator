<?php

if (!function_exists('wp_content_translator_is_language_set')) {
    function wp_content_translator_is_language_set()
    {
        return \ContentTranslator\Switcher::isLanguageSet();
    }
}
