<?php

$option = (object) array(
    'translate' => true,
    'translate_numeric' => false,
    'translate_hidden' => false,
    'translatable' => array(),
    'untranslatable' => array()
);

return (object) apply_filters('wp-content-translator/configuration/site_option', $option);
