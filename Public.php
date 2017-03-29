<?php

if (!function_exists('wp_content_translator_is_language_set')) {
    /**
     * Check if language is set
     * @return bool
     */
    function wp_content_translator_is_language_set() : bool
    {
        return (bool) \ContentTranslator\Switcher::isLanguageSet();
    }
}

if (!function_exists('wp_content_translator_current_language')) {
    /**
     * Gets the current language and it's attributes
     * @return \stdClass
     */
    function wp_content_translator_current_language() //: \stdClass
    {
        return \ContentTranslator\Switcher::$currentLanguage;
    }
}

if (!function_exists('wp_content_translator_languages')) {
    /**
     * Get a list of all languages matching the "level"
     * Available levels: all, active, installed, uninstalled
     * @param  string $level Level
     * @return array
     */
    function wp_content_translator_languages(string $level = 'active') : array
    {
        if (!method_exists('\ContentTranslator\Language', $level)) {
            $level = 'active';
        }

        $languages = \ContentTranslator\Language::$level();
        $currentUrl = parse_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        foreach ($languages as $language) {

            if (empty($language)) {
                continue;
            }

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


            if (is_object(\ContentTranslator\Switcher::$currentLanguage)) {
                if (\ContentTranslator\Switcher::$currentLanguage->code === $language->code) {
                    $language->isCurrent = true;
                }
            }
        }

        return (array) $languages;
    }
}

if (!function_exists('wp_content_translator_unparse_url')) {
    /**
     * Unparses url (parse_url() function)
     * @param  array  $string The array of the parse_url() function
     * @return string
     */
    function wp_content_translator_unparse_url(array $parsedUrl) : string
    {
        $host     = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user     = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass     = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query    = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return "//$user$pass$host$port$path$query$fragment";
    }
}

if (!function_exists('wp_content_translator_language_selector')) {
    /**
     * Renders a language selector
     * @param  string|null  $wrapper   Wrapper template
     * @param  string|null  $element   Element template
     * @param  bool|boolean $echo      Echo or not
     * @param  array|null   $languages Languages to use in selector
     * @return string                  HTML Markup of the selector
     */
    function wp_content_translator_language_selector(string $wrapper = null, string $element = null, bool $echo = true, array $languages = null)
    {
        return \ContentTranslator\Switcher::selector($wrapper, $element, $echo, $languages);
    }
}
