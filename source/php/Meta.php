<?php

namespace ContentTranslator;

class Meta Extends Entity\Translate
{

    public function __construct()
    {

        // Get actions
        add_filter('get_post_metadata',array($this,'get'),4,1);

        // Add and update actions
        foreach (array('add','update') as $action) {
            add_filter($action . '_post_metadata', array($this, 'save'));
        }

    }

    public function save(null, $object_id, $meta_key, $single ) {

        return null;
    }

    public function get(null, $object_id, $meta_key, $single )
    {
        if(!$this->isLangualMeta($meta_key)) {
            return get_metadata('post', $object_id, $this->createLangualMetaKey($meta_key), $single);
        }
        return null;
    }

    private function isLangualMeta($meta_key) {
        return substr($meta_key, -strlen("_".\ContentTranslator\Switcher::$currentLanguage->code) == "_".\ContentTranslator\Switcher::$currentLanguage->code ? true : false;
    }

    private function createLangualMetaKey ($meta_key) {
        return $meta_key."_".\ContentTranslator\Switcher::$currentLanguage->code;
    }

}
