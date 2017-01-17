<?php

namespace ContentTranslator;

class Meta extends Entity\Translate
{
    public function __construct()
    {
        if (WCT_TRANSLATE_META) {
            parent::__construct();
            add_filter('get_post_metadata', array($this, 'get'), 1, 4);
            add_filter('update_post_metadata', array($this, 'save'), 1, 4);
            add_filter('add_post_metadata', array($this, 'save'), 1, 4);
        }
    }

    /**
     * Install procedure
     * @param  string $language Language to install
     * @return bool
     */
    public static function install(string $language) : bool
    {
        // Nothing to do for the install
        return true;
    }

    /**
     * Uninstall procedure
     * @param  string $language Language to install
     * @return bool]
     */
    public static function uninstall(string $language) : bool
    {
        // Remove all related meta?
        return true;
    }

    public function save($null, int $post_id, string $meta_key, $meta_value) // : ?bool  - Waiting for 7.1 enviroments to "be out there".
    {

        if (!$this->isLangual($meta_key) && $this->shouldTranslate($meta_key, $meta_value)) {

            //Create meta key
            $langual_meta_key = $this->createLangualKey($meta_key);

            //Update post meta
            if (!$this->identicalToBaseLang($meta_key, $meta_value, $post_id)) {
                update_post_meta($post_id, $langual_meta_key, $meta_value);
                return true;
            }

            //Clean meta that equals base language
            if ($this->identicalToBaseLang($meta_key, $meta_value, $post_id)) {
                delete_post_meta($post_id, $langual_meta_key);
                return true;
            }
        }

        return null;
    }

    public function get($type, int $post_id, string $meta_key, bool $single) // : ?string - Waiting for 7.1 enviroments to "be out there".
    {
        if (!$this->isLangual($meta_key) && $this->shouldTranslate($meta_key)) {

            remove_filter('get_post_metadata', array($this, 'get'), 1);
            $translation = get_post_meta($post_id, $this->createLangualKey($meta_key));
            add_filter('get_post_metadata', array($this, 'get'), 1, 4);

            if (!TRANSLATE_FALLBACK && implode("", $translation) == "") {
                return "";              // Abort and return empty (no fallback)
            } elseif (TRANSLATE_FALLBACK && implode("", $translation) == "") {
                return null;            // Continiue normal procedure (fallback to base lang)
            } else {
                return $translation;    // Translation found, return value
            }
        }

        return null;
    }

    private function shouldTranslate(string $meta_key, $meta_value = null) : bool
    {
        if (in_array($meta_key, WCT_TRANSLATABLE_META)) {
            return true;
        }

        if (in_array($meta_key, WCT_UNTRANSLATEBLE_META)) {
            return false;
        }

        if (!WTC_TRANSLATE_HIDDEN_META && substr($meta_key, 0, 1) == "_") {
            return false;
        }

        if (!WCT_TRANSLATE_NUMERIC_META && is_numeric($meta_value) && $meta_value != null) {
            return false;
        }

        return apply_filters('wp-content-translator/meta/should_translate_default', true, $meta_key);
    }

    private function identicalToBaseLang(string $meta_key, $translated, int $post_id) : bool
    {
        remove_filter('get_post_metadata', array($this, 'get'), 1);
        $default = get_post_meta($post_id, $meta_key);
        add_filter('get_post_metadata', array($this, 'get'), 1, 4);

        if (trim($default) == trim($translated)) {
            return true;
        }

        return false;
    }
}
