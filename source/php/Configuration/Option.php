<?php

$option = (object) array(
    'translate' => true,
    'translate_numeric' => false,
    'translate_hidden' => false,
    'untranslatable' => array(
        ContentTranslator\Admin\Options::$optionKey['active'],
        ContentTranslator\Admin\Options::$optionKey['installed'],
        'siteurl',
        'home',
        'users_can_register',
        'permalink_structure',
        'rewrite_rules',
        'active_plugins',
        'template',
        'stylesheet',
        'theme_switched',
        'html_type',
        'default_role',
        'default_comments_page',
        'comment_order',
        'WPLANG',
        'cron',
        'nestedpages_posttypes',
        'nestedpages_version',
        'nestedpages_menusync',
        'modularity-options',
        'acf_version'
    ),
    'translatable' => array()
);

return (object) apply_filters('wp-content-translator/configuration/option', $option);