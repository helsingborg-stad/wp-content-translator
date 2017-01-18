<?php

$config = (object) array(
    'translate_comment' => true,
    'translate_comment_meta' => true
);

return (object) apply_filters('wp-content-translator/configuration/comment', $config);
