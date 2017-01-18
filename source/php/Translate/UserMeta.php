<?php

namespace ContentTranslator\Translate;

class UserMeta extends Meta
{
    public function __construct()
    {
        parent::__construct();

        if ($this->configuration->usermeta->translate) {
            parent::__construct('user');
        }
    }
}
