<?php

$config = (object) array(
    'translate' => true,
    'untranslatable_types' => array(
        'revision'
    )
);

return (object) apply_filters('wp-content-translator/configuration/post', $config);
