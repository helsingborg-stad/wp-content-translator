<?php

namespace ContentTranslator;

class Option extends Entity\Translate
{
    protected $lang;

    public function __construct()
    {
        if (\ContentTranslator\Switcher::isLanguageSet() && !\ContentTranslator\Language::isDefault()) {
            $this->lang = \ContentTranslator\Switcher::$currentLanguage->code;
            add_action('init', array($this, 'hook'));

            add_filter('pre_update_option', array($this, 'preUpdateOption'), 10, 3);
        }
    }

    /**
     * Hooks get method on all options
     * @return void
     */
    public function hook()
    {
        $options =  $this->getOptionNames();

        foreach ($options as $option) {
            add_filter('option_' . $option, array($this, 'get'), 10, 2);
        }
    }

    /**
     * Gets the translated version (if available) of each option field
     * @param  mixed $value   The default value
     * @param  string $option The options field key
     * @return mixed          The translated value
     */
    public function get($value, $option)
    {
        if (!$this->shouldTranslate($option, $value)) {
            return $value;
        }

        remove_filter('option_' . $option, array($this, 'get'), 10);
        $translated = get_option($this->createLangualKey($option), $value);
        add_filter('option_' . $option, array($this, 'get'), 10, 2);

        return $translated;
    }

    /**
     * Handle save options
     * TODO: For some reason the actions before/after does not work with acf.
     *
     * @param  mixed $value     The option value
     * @param  string $option   Option name
     * @param  mixed $oldValue  Old option value (before update)
     * @return mixed            Returs the oldValue to tell the parent function to return false
     */
    public function preUpdateOption($value = null, $option = null, $oldValue = null)
    {
        if ($this->shouldTranslate($option, $value) && !$this->identicalToBaseLang($option, $value)) {
            if (!isset($_POST['acf'])) {
                add_action('wp-content-translator/option/before_update_option', $option, $value);
            }

            remove_filter('pre_update_option', array($this, 'preUpdateOption'), 10);
            update_option($this->createLangualKey($option), $value);
            add_filter('pre_update_option', array($this, 'preUpdateOption'), 10, 3);

            if (!isset($_POST['acf'])) {
                add_action('wp-content-translator/option/after_update_option', $option, $value);
            }
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
        if (in_array($key, WTC_TRANSLATABLE_OPTION)) {
            return true;
        }

        if (in_array($key, WTC_UNTRANSLATEBLE_OPTION)) {
            return false;
        }

        if (!WCT_TRANSLATE_NUMERIC_OPTION && is_numeric($value) && $value != null) {
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

        if (WTC_TRANSLATE_HIDDEN_OPTION) {
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
     * Check if the given value is the same value stored in the database
     * @param  string $key        Option name
     * @param  mixed  $translated Option value
     * @return bool
     */
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
