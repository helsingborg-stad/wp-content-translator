<?php

namespace ContentTranslator;

class Meta
{

    protected $lang;
    protected $db;

    public function __construct()
    {
        if (\ContentTranslator\Switcher::isLanguageSet() && !\ContentTranslator\Language::isDefault()) {

            global $wpdb;

            $this->lang = \ContentTranslator\Switcher::$currentLanguage->code;
            $this->db   = $wpdb;

            add_filter('get_post_metadata', array($this,'get'), 1, 4);
            add_filter('update_post_metadata', array($this,'save'), 1, 4);
            add_filter('add_post_metadata', array($this,'save'), 1, 4);
        }
    }

    public function save($null, $post_id, $meta_key, $meta_value)
    {
        if(!$this->isLangualMeta($meta_key) && $this->shouldTranslate($meta_key) && !$this->identicalToBaseLang($meta_key, $meta_value, $post_id)) {
            update_post_meta($post_id, $this->createLangualMetaKey($meta_key), $meta_value);
            return true;
        } elseif(!$this->isLangualMeta($meta_key) && $this->shouldTranslate($meta_key) && $this->identicalToBaseLang($meta_key, $meta_value, $post_id)) {
            //TODO: Activating this also clears base language. IT shoould not.
            //      Identical to base lang seems to be broken too.
            //delete_post_meta($post_id, $this->createLangualMetaKey($meta_key));
        }

        return null;
    }

    public function get($type, $post_id, $meta_key, $single)
    {

        if(!$this->isLangualMeta($meta_key) && $this->shouldTranslate($meta_key)) {

            $translation =  $this->db->get_col(
                                $this->db->prepare( "SELECT meta_value FROM {$this->db->postmeta} WHERE post_id = %d AND meta_key = %s", $post_id, $this->createLangualMetaKey($meta_key))
                            );

            if (!TRANSLATE_FALLBACK && implode("", $translation) == "") {
                return "";              // Abort and return empty (no fallback)
            } elseif (TRANSLATE_FALLBACK && implode("", $translation) == "") {
                return null;            // Continiue normal procedure (fallback to base lang)
            } {
                return $translation;    // Translation found, return value
            }
        }

        return null;

    }

    private function shouldTranslate($meta_key)
    {

        if(in_array($meta_key, TRANSLATABLE_META)) {
            return true;
        }

        if(!TRANSLATE_HIDDEN_META && substr($meta_key, 0, 1) == "_") {
            return false;
        }

        if(in_array($meta_key, UNTRANSLATEBLE_META)) {
            return false;
        }

        return apply_filters('wp-content-translator/meta/should_translate_default', true, $meta_key);

    }

    private function isLangualMeta($meta_key)
    {
        return substr($meta_key, -strlen(TRANSLATE_DELIMITER.$this->lang)) == TRANSLATE_DELIMITER.$this->lang ? true : false;
    }

    private function createLangualMetaKey ($meta_key)
    {
        return $meta_key.TRANSLATE_DELIMITER.$this->lang;
    }

    private function identicalToBaseLang($meta_key, $meta_value, $post_id) {

        $translation =  $this->db->get_col(
                                $this->db->prepare( "SELECT meta_value FROM {$this->db->postmeta} WHERE post_id = %d AND meta_key = %s", $post_id, $this->createLangualMetaKey($meta_key))
                            );

        if ($translation == $meta_value) {
            return true;
        }

        return false;
    }

}
