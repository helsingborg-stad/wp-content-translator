<?php

namespace ContentTranslator;

class User Extends Meta
{
    public function __construct()
    {
        if (WCT_TRANSLATE_USER) {
            parent::__construct();
            add_filter('get_user_metadata', array($this, 'get'), 1, 4);
            add_filter('update_user_metadata', array($this, 'save'), 1, 4);
            add_filter('add_user_metadata', array($this, 'save'), 1, 4);
        }
    }
}
