<?php

namespace ContentTranslator\Translate;

class Option extends \ContentTranslator\Entity\Translate
{
    public function __construct()
    {
        parent::__construct();

        if ($this->configuration->option->translate) {
            add_action('init', array($this, 'hook'));
            add_filter('pre_update_option', array($this, 'preUpdateOption'), 10, 3);
        }
    }

    /**
     * Install procedure
     * @param  string $language Language to install
     * @return bool
     */
    public static function install(string $language) : bool
    {
        do_action('wp-content-translator/option/install', $language);
        return true;
    }

    /**
     * Is installed?
     * @param  string  $language Language to check
     * @return boolean
     */
    public static function isInstalled(string $language) : bool
    {
        return apply_filters('wp-content-translator/option/is_installed', true);
    }

    /**
     * Uninstall procedure
     * Removes meta of the removed language
     * @param  string $language Language to install
     * @return bool]
     */
    public static function uninstall(string $language) : bool
    {
        do_action('wp-content-translator/option/uninstall', $language);

        // Bail if we should not remove the meta
        if (!apply_filters('wp-content-translator/option/remove_option_when_uninstalling_language', true)) {
            return false;
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '%_' . $language)
        );

        return true;
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
    public function get($value, string $option)
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
    public function preUpdateOption($value = null, string $option = null, $oldValue = null)
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

        // If it's an content translator option reutrn the new value
        if (in_array($option, \ContentTranslator\Admin\Options::$optionKey)) {
            return $value;
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
    public function shouldTranslate(string $key, $value) : bool
    {
        if (in_array($key, $this->configuration->option->untranslatable)) {
            return false;
        }

        if (!$this->configuration->option->translate_numeric && is_numeric($value) && $value != null) {
            return false;
        }

        if (!$this->configuration->option->translate_hidden && substr($key, 0, 1) == "_") {
            return false;
        }

        if (in_array($key, $this->configuration->option->translatable)) {
            return true;
        }

        return apply_filters('wp-content-translator/option/should_translate_default', true, $key);
    }

    /**
     * Get all option names from options table (db)
     * @return array
     */
    public function getOptionNames() : array
    {
        $options = array();

        if ($this->configuration->option->translate_hidden) {
            $options = $this->db->get_results("SELECT option_name FROM {$this->db->options} GROUP BY option_name ORDER BY option_name ASC");
        } else {
            $options = $this->db->get_results("SELECT option_name FROM {$this->db->options} WHERE option_name NOT LIKE '\_%' GROUP BY option_name ORDER BY option_name ASC");
        }

        $optionsArray = array();
        foreach ($options as $option) {
            $optionsArray[] = $option->option_name;
        }

        return $optionsArray;
    }

    /**
     * Check if the given value is the same value stored in the database
     * @param  string $option     Option name (key)
     * @param  mixed  $translated Option value
     * @return bool
     */
    private function identicalToBaseLang(string $option, $translated) : bool
    {
        remove_filter('option_' . $option, array($this, 'get'), 10);
        $default = get_option($option, $translated);
        add_filter('option_' . $option, array($this, 'get'), 10, 2);

        if ($default === $translated) {
            return true;
        }

        return false;
    }
}
