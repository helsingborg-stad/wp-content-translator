<?php

namespace ContentTranslator\Translate;

class User extends Meta
{
    public function __construct()
    {
        if (WCT_TRANSLATE_USER_META) {
            parent::__construct('user');
        }
    }
}
