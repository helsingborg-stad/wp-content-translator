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

    public function save() {

    }

    public function get(null, $object_id, $meta_key, $single ) {
        if (!$this->isLangualMeta($meta_key)) {
            return get_metadata('post', $object_id, $this->createLangualMetaKey($meta_key), $single);
        }

        return null;
    }

    private function isLangualMeta($meta_key) {
        return substr($meta_key, -strlen('_' . $this->lang)) == '_' . $this->lang ? true : false;
    }

    private function createLangualMetaKey ($meta_key) {
        return $meta_key . '_' . $this->lang;
    }
}
