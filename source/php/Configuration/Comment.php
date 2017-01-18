<?php

$config = (object) array(
    'translate' => true,
    'translate_hidden' => false,
    'translate_numeric' => false,
    'untranslatable' => array(),
    'translatable' => array()
);

return (object) apply_filters('wp-content-translator/configuration/comment', $config);
