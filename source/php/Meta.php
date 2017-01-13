<?php

namespace ContentTranslator;

class Meta Extends Entity\Translate
{

    protected $lang;

    public function __construct()
    {
        if (\ContentTranslator\Switcher::isLanguageSet()) {
            $this->lang = \ContentTranslator\Switcher::$currentLanguage['code'];
            add_filter('get_post_metadata', array($this,'get'), 4, 1);
        }
    }

    public function save(null, $object_id, $meta_key, $single )
    {

        return null;
    }

    public function get(null, $object_id, $meta_key, $single )
    {

        if(!$this->isLangualMeta($meta_key) && $this->shouldTranslate($meta_key)) {

            $translation =  get_metadata('post', $object_id, $this->createLangualMetaKey($meta_key), $single);

            if (!TRANSLATE_FALLBACK && $translation == "") {
                return "";      // Abort and return empty
            } elseif (TRANSLATE_FALLBACK && $translation == "") {
                return null;    // Continiue normal procedure
            } {
                return $translation; // Not empty, return value
            }
        }

        return null;
    }

    private function shouldTranslate($meta_key)
    {
        if(!TRANSLATE_HIDDEN_META && substr($meta_key, 1) == "_") {
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
}
