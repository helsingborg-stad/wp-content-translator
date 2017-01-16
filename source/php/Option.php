<?php

namespace ContentTranslator;

class Option
{
    protected $lang;

    public function __construct()
    {
        if (\ContentTranslator\Switcher::isLanguageSet() && !\ContentTranslator\Language::isDefault()) {
            $this->lang = \ContentTranslator\Switcher::$currentLanguage->code;
            add_action('init', array($this, 'hook'));

            add_filter('pre_update_option', array($this, 'preUpdateOption'), 10, 3);
            //add_action('update_option', array($this, 'updateOption'), 10, 3);
            //add_action('add_option', array($this, 'addOption'));
        }
    }

    public function hook()
    {
        $options =  $this->getOptionNames();

        foreach ($options as $option) {
            add_filter('option_' . $option, array($this, 'get'), 10, 2);
        }
    }

    public function get($value, $option)
    {
        if (!$this->shouldTranslate($option, $value)) {
            return $value;
        }

        remove_filter('option_' . $option, array($this, 'get'));
        $translated = get_option($this->createLangualKey($option), $value);
        add_filter('option_' . $option, array($this, 'get'), 10, 2);

        return $translated;
    }

    public function preUpdateOption($value, $option, $oldValue)
    {
        if ($this->shouldTranslate($option, $value) && !$this->identicalToBaseLang($option, $value)) {
            remove_filter('pre_update_option', array($this, 'preUpdateOption'));
            update_option($this->createLangualKey($option), $value);
            add_filter('pre_update_option', array($this, 'preUpdateOption'), 10, 3);
        }

        // Return old value to stop make the update_option function
        // return false
        return $oldValue;
    }

    public function shouldTranslate($key, $value) : bool
    {
        if (in_array($key, TRANSLATABLE_OPTION)) {
            return true;
        }

        if (in_array($key, UNTRANSLATEBLE_OPTION)) {
            return false;
        }

        if (is_numeric($value)) {
            return false;
        }

        return apply_filters('wp-content-translator/option/should_translate_default', true, $key);
    }

    /**
     * Get all option names from options table (db)
     * @return array
     */
    public function getOptionNames() : array
    {
        global $wpdb;

        $options = array();

        if (TRANSLATE_HIDDEN_META) {
            $options = $wpdb->get_results("SELECT option_name FROM $wpdb->options GROUP BY option_name ORDER BY option_name ASC");
        } else {
            $options = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name NOT LIKE '\_%' GROUP BY option_name ORDER BY option_name ASC");
        }

        $optionsArray = array();
        foreach ($options as $option) {
            $optionsArray[] = $option->option_name;
        }

        return $optionsArray;
    }

    /**
     * Creates a language specific meta/options key
     * TODO: Move to shared functions(?)
     * @param  string $key The meta/option key
     * @return string      Langual meta/option key
     */
    private function createLangualKey(string $key) : string
    {
        return $key . TRANSLATE_DELIMITER . $this->lang;
    }

    private function isLangualOption($key)
    {
        return substr($key, -strlen(TRANSLATE_DELIMITER . $this->lang)) == TRANSLATE_DELIMITER . $this->lang ? true : false;
    }

    private function identicalToBaseLang(string $key, $translated) : bool
    {
        global $wpdb;

        $default =  $wpdb->get_var($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $key));

        if ($default === $translated) {
            return true;
        }

        return false;
    }
}
