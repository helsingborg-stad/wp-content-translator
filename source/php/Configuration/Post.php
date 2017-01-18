<?php

$config = (object) array(
    'translate_posts' => true,
    'untranslatable_post_types' => array(
        'revision'
    )
);

return (object) apply_filters('wp-content-translator/configuration/post', $config);
