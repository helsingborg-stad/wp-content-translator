<?php

$config = (object) array(
    'translate' => true,
    'translate_numeric' => false,
    'translate_hidden' => false,
    'translatable' => array(),
    'untranslatable' => array(
        'active_sitewide_plugins',
        'recently_activated',
        'WPLANG',
        'acf_pro_license'
    )
);

return (object) apply_filters('wp-content-translator/configuration/site_option', $config);
