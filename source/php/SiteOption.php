<?php

namespace ContentTranslator;

class SiteOption extends Entity\Translate
{
    public function __construct()
    {
        parent::__construct();

        if (function_exists('is_multisite') && is_multisite() && \ContentTranslator\Switcher::isLanguageSet() && !\ContentTranslator\Language::isDefault()) {
            add_action('init', array($this, 'hook'));
            add_filter('get_network', array($this, 'getNetwork'));
        }
    }

    /**
     * Hooks get method on all options
     * @return void
     */
    public function hook()
    {
        $options = $this->getOptionNames();

        foreach ($options as $option) {
            add_filter('site_option_' . $option, array($this, 'get'), 10, 3);
            add_filter('pre_update_site_option_' . $option, array($this, 'preUpdateSiteOption'), 10, 4);
        }
    }

    /**
     * Gets the translated version (if available) of each option field
     * @param  mixed $value   The default value
     * @param  string $option The options field key
     * @return mixed          The translated value
     */
    public function get($value, $option, $networkId = null)
    {
        if (!$this->shouldTranslate($option, $value)) {
            return $value;
        }

        remove_filter('site_option_' . $option, array($this, 'get'), 10);
        $translated = get_site_option($this->createLangualKey($option), $value);
        add_filter('site_option_' . $option, array($this, 'get'), 10, 2);

        return $translated;
    }

    /**
     * Translate site_name in get_network() function
     * @param  WP_Network $network WP_Network object
     * @return WP_Network          Modified WP_Network object
     */
    public function getNetwork($network) : \WP_Network
    {
        remove_filter('get_network', array($this, 'getNetwork'), 10);

        $option = 'site_name';

        remove_filter('site_option_' . $option, array($this, 'get'), 10);
        $translated = get_site_option($this->createLangualKey($option), $network->site_name);
        add_filter('site_option_' . $option, array($this, 'get'), 10, 3);

        $network->site_name = $translated;
        return $network;
    }

    /**
     * Handle save options     *
     * @param  mixed $value     The option value
     * @param  string $option   Option name
     * @param  mixed $oldValue  Old option value (before update)
     * @return mixed            Returs the oldValue to tell the parent function to return false
     */
    public function preUpdateSiteOption($value, $oldValue, $option, $networkId = null)
    {
        if ($this->shouldTranslate($option, $value) && !$this->identicalToBaseLang($option, $value, $networkId)) {
            if (is_null($networkId)) {
                $networkId = get_current_network_id();
            }

            remove_filter('pre_update_site_option_' . $option, array($this, 'preUpdateSiteOption'), 10);
            update_site_option($this->createLangualKey($option), $value);
            add_filter('pre_update_site_option_' . $option, array($this, 'preUpdateSiteOption'), 10, 3);
        }

        // Return old value to stop make the update_option function
        // return false
        return $oldValue;
    }

    /**
     * Tells if a option shoud be translated
     * @param  string $key   Option name
     * @param  mixed $value  Option value
     * @return bool
     */
    public function shouldTranslate($key, $value) : bool
    {
        if (in_array($key, WTC_TRANSLATABLE_SITE_OPTION)) {
            return true;
        }

        if (in_array($key, WTC_UNTRANSLATEBLE_SITE_OPTION)) {
            return false;
        }

        if (!WCT_TRANSLATE_NUMERIC_SITE_OPTION && is_numeric($value) && $value != null) {
            return false;
        }

        return apply_filters('wp-content-translator/option/should_translate_default', true, $key);
    }

    /**
     * Get all site options avaiable in the database
     * @return array
     */
    public function getOptionNames() : array
    {
        $options = array();
        if (WTC_TRANSLATE_HIDDEN_OPTION) {
            $options = $this->db->get_results($this->db->prepare("SELECT meta_key FROM {$this->db->sitemeta} WHERE site_id = %d GROUP BY meta_key ORDER BY meta_key ASC", get_current_network_id()));
        } else {
            $options = $this->db->get_results($this->db->prepare("SELECT meta_key FROM {$this->db->sitemeta} WHERE site_id = %d AND meta_key NOT LIKE '%s' GROUP BY meta_key ORDER BY meta_key ASC", get_current_network_id(), '\_%'));
        }

        $optionsArray = array();
        foreach ($options as $option) {
            $optionsArray[] = $option->meta_key;
        }

        return $optionsArray;
    }

    /**
     * Check if the given value is the same value stored in the database
     * @param  string $key        Option name
     * @param  mixed  $translated Option value
     * @return bool
     */
    private function identicalToBaseLang(string $key, $translated, int $networkId = null) : bool
    {
        if (is_null($networkId)) {
            $networkId = get_current_network_id();
        }

        $default =  $this->db->get_var($this->db->prepare("SELECT meta_value FROM {$this->db->sitemeta} WHERE site_id = %d AND meta_key = %s LIMIT 1", $networkId, $key));

        if ($default === $translated) {
            return true;
        }

        return false;
    }
}
