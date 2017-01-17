<?php

namespace ContentTranslator\Translate;

class CommentMeta extends Meta
{
    public function __construct()
    {
        if (WCT_TRANSLATE_COMMENT_META) {
            parent::__construct('comment');
        }
    }
}
