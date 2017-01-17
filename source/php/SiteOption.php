<?php

namespace ContentTranslator;

class SiteOption
{
    public function __construct()
    {
        if (function_exists('is_multisite') && is_multisite() && \ContentTranslator\Switcher::isLanguageSet() && !\ContentTranslator\Language::isDefault()) {
            add_action('init', array($this, 'hook'));
        }
    }

    public function get($value, $option, $networkId)
    {
        return $value;
    }

    public function hook()
    {
        $options = $this->getOptionNames();

        foreach ($options as $option) {
            add_filter('site_option_' . $option, array($this, 'get'), 10, 3);
        }
    }

    public function getOptionNames()
    {
        global $wpdb;

        $options = array();
        if (WTC_TRANSLATE_HIDDEN_OPTION) {
            $options = $wpdb->get_results($wpdb->prepare("SELECT meta_key FROM $wpdb->sitemeta WHERE site_id = %d GROUP BY meta_key ORDER BY meta_key ASC", get_current_network_id()));
        } else {
            $options = $wpdb->get_results($wpdb->prepare("SELECT meta_key FROM $wpdb->sitemeta WHERE site_id = %d AND meta_key NOT LIKE '%s' GROUP BY meta_key ORDER BY meta_key ASC", get_current_network_id(), '\_%'));
        }

        $optionsArray = array();
        foreach ($options as $option) {
            $optionsArray[] = $option->meta_key;
        }

        return $optionsArray;
    }
}
