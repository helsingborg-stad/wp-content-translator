<?php

$config = (object) array(
    'translate' => true,
    'untranslatable_post_types' => array(
        'revision',
        'acf-field-group',
        'acf-field'
    )
);

return (object) apply_filters('wp-content-translator/configuration/post', $config);
