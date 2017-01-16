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

if (!function_exists('wp_content_translator_languages')) {
    function wp_content_translator_languages(string $level = 'active')
    {
        if (!method_exists('\ContentTranslator\Language', $level)) {
            $level = 'active';
        }

        $languages = \ContentTranslator\Language::$level();
        $currentUrl = parse_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        foreach ($languages as $language) {
            $url = $currentUrl;

            if (isset($url['query'])) {
                if (strlen($url['query']) > 0) {
                    $url['query'] .= '&';
                }

                $url['query'] .= http_build_query(array('lang' => $language->code));
            } else {
                $url['query'] = http_build_query(array('lang' => $language->code));
            }

            parse_str($url['query'], $url['query']);
            $url['query'] = http_build_query($url['query']);

            $language->url = wp_content_translator_unparse_url($url);

            $language->isCurrent = false;
            if (\ContentTranslator\Switcher::$currentLanguage->code === $language->code) {
                $language->isCurrent = true;
            }
        }

        return $languages;
    }
}

if (!function_exists('wp_content_translator_unparse_url')) {
    function wp_content_translator_unparse_url(array $parsed_url)
    {
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "//$user$pass$host$port$path$query$fragment";
    }
}
