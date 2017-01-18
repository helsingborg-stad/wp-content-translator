<?php

namespace ContentTranslator\Translate;

class CommentMeta extends Meta
{
    public function __construct()
    {
        if ($this->configuration->comment->translate) {
            parent::__construct('comment');
        }
    }
}
