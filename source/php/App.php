<?php

namespace ContentTranslator;

class App
{
    public static $defaultWpdbTables;

    public function __construct()
    {
        /*  Hook format:
            wp-content-translator/option/translate_fallback
        */

        // Hooks
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));

        // Init run
        $this->generalConfiguration();
        $this->metaConfiguration();
        $this->postConfiguration();
        $this->optionConfiguration();

        // Setup wpdb
        global $wpdb;
        self::$defaultWpdbTables = array(
            'posts' => $wpdb->posts
        );

        // Core
        new Switcher();
        new Post();
        new Meta();
        new Option();

        // Admin
        new Admin\Options();
        new Admin\AdminBar();

        // Helpers
        new Helper\Database();

    }

    public function adminEnqueue()
    {
        wp_enqueue_style('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/css/wp-content-translator-admin.min.css', null, '1.0.0');
        wp_enqueue_script('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/js/wp-content-translator-admin.dev.js', array('jquery'), '1.0.0', true);
    }

    public function generalConfiguration () {
        define('TRANSLATE_FALLBACK', apply_filters('wp-content-translator/option/translate_fallback', true));
        define('TRANSLATE_DELIMITER', apply_filters('wp-content-translator/option/translate_delimeter', "_"));
    }

    public function metaConfiguration () {

        define('WCT_UNTRANSLATEBLE_META', (array) apply_filters('wp-content-translator/option/untranslatable_meta', array(
            '_edit_lock',
            'modularity-modules',
            'modularity-sidebar-options'
        )));

        define('WCT_TRANSLATABLE_META', (array) apply_filters('wp-content-translator/option/translatable_meta', array(
            '_aioseop_title',
            '_aioseop_description',
            '_aioseop_titleatr'
        )));

        define('WTC_TRANSLATE_HIDDEN_META',     (bool) apply_filters('wp-content-translator/option/translate_hidden_meta'   , false));
        define('WCT_TRANSLATE_META',            (bool) apply_filters('wp-content-translator/option/translate_meta'          , true));
        define('WCT_TRANSLATE_NUMERIC_META',    (bool) apply_filters('wp-content-translator/option/translate_numeric_meta'  , false));

    }

    public function postConfiguration () {

    }

    public function optionConfiguration () {

        define('WTC_UNTRANSLATEBLE_OPTION', (array) apply_filters('wp-content-translator/option/untranslatable_options', array(
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
            'acf_version',
            'wp-content-translator-active',
            'wp-content-translator-installed'
        )));

        define('TRANSLATABLE_OPTION', (array) apply_filters('wp-content-translator/option/translatable_options', array(

        )));

        define('WCT_TRANSLATE_OPTION',          (bool) apply_filters('wp-content-translator/option/translate_option'            , true));
        define('WCT_TRANSLATE_NUMERIC_OPTION',  (bool) apply_filters('wp-content-translator/option/translate_numeric_option'    , false));
        define('WTC_TRANSLATE_HIDDEN_OPTION',   (bool) apply_filters('wp-content-translator/option/translate_hidden_option'     , false));

    }

}
