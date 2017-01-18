<?php

$option = array(
    'translate_option' => true,
    'translate_numeric_option' => false,
    'translate_hidden_option' => false,
    'untranslatable_option' => array(
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
    'translatable_option' => array()
);

$siteOption = array(
    'translate_site_option' => true,
    'translate_numeric_site_option' => false,
    'translate_hidden_site_option' => false,
    'translatable_site_option' => array(),
    'untranslatable_site_option' => array()
);

return (object) apply_filters('wp-content-translator/configuration/option', (object) array_merge($option, $siteOption));
