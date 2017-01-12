<?php

namespace ContentTranslator;

class Meta Extends Entity\Translate
{

    protected $lang;

    public function __construct()
    {

        $this->lang = "en";

        add_filter('get_post_metadata',array($this,'get'),4,1);
    }

    public function save() {

    }

    public function get(null, $object_id, $meta_key, $single ) {
        if(!$this->isLangualMeta()) {
            return get_metadata('post', $object_id, $meta_key."_".$this->lang, $single);
        }
        return null;
    }

    private function isLangualMeta($meta_key) {
        return substr($meta_key, -strlen("_".$this->lang)) == "_".$this->lang ? true : false;
    }
}
