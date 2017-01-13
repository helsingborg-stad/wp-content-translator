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

    public function save(null, $object_id, $meta_key, $single ) {

        return null;
    }

    public function get(null, $object_id, $meta_key, $single ) {

        if(!$this->isLangualMeta($meta_key)) {

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

    private function isLangualMeta($meta_key) {
        return substr($meta_key, -strlen(TRANSLATE_DELIMITER.$this->lang)) == TRANSLATE_DELIMITER.$this->lang ? true : false;
    }

    private function createLangualMetaKey ($meta_key) {
        return $meta_key.TRANSLATE_DELIMITER.$this->lang;
    }
}
