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
        $this->userConfiguration();
        $this->commentConfiguration();

        // Setup wpdb
        global $wpdb;
        self::$defaultWpdbTables = array(
            'posts' => $wpdb->posts
        );

        // Core
        new Switcher();

        // Translate
        if (\ContentTranslator\Switcher::isLanguageSet() && !\ContentTranslator\Language::isDefault()) {
            new Post();
            new Meta();
            new Option();
            new SiteOption();
        }

        // Admin
        new Admin\Options();
        new Admin\AdminBar();

        // Helpers
        new Helper\Database();

    }

    public function adminEnqueue() // : void - Waiting for 7.1 enviroments to "be out there".
    {
        wp_enqueue_style('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/css/wp-content-translator-admin.min.css', null, '1.0.0');
        wp_enqueue_script('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/js/wp-content-translator-admin.dev.js', array('jquery'), '1.0.0', true);
    }

    public function generalConfiguration () // : void - Waiting for 7.1 enviroments to "be out there".
    {
        define('TRANSLATE_FALLBACK', apply_filters('wp-content-translator/option/translate_fallback', true));
        define('TRANSLATE_DELIMITER', apply_filters('wp-content-translator/option/translate_delimeter', "_"));
    }

    public function metaConfiguration () // : void - Waiting for 7.1 enviroments to "be out there".
    {

        define('WCT_UNTRANSLATEBLE_META', (array) apply_filters('wp-content-translator/option/untranslatable_meta', array(
            '_edit_lock',
            '_edit_last',
            '_wp_page_template',
            'modularity-modules',
            'modularity-sidebar-options'
        )));

        define('WCT_TRANSLATABLE_META', (array) apply_filters('wp-content-translator/option/translatable_meta', array(
            '_aioseop_title',
            '_aioseop_description'
        )));

        define('WTC_TRANSLATE_HIDDEN_META',     (bool) apply_filters('wp-content-translator/option/translate_hidden_meta'   , false));
        define('WCT_TRANSLATE_META',            (bool) apply_filters('wp-content-translator/option/translate_meta'          , true));
        define('WCT_TRANSLATE_NUMERIC_META',    (bool) apply_filters('wp-content-translator/option/translate_numeric_meta'  , false));

    }

    public function postConfiguration () // : void - Waiting for 7.1 enviroments to "be out there".
    {
        define('WCT_UNTRANSLATEBLE_POST_TYPES',  (bool) apply_filters('wp-content-translator/option/untranslatable_post_types'  , array(

        )));

        define('WCT_TRANSLATE_POSTS',           (bool) apply_filters('wp-content-translator/option/translate_posts'         , true));
    }

    public function optionConfiguration () // : void - Waiting for 7.1 enviroments to "be out there".
    {

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

        define('WTC_TRANSLATABLE_OPTION', (array) apply_filters('wp-content-translator/option/translatable_options', array(

        )));

        define('WCT_TRANSLATE_SITE_OPTION',          (bool) apply_filters('wp-content-translator/option/translate_option'            , true));
        define('WCT_TRANSLATE_NUMERIC_SITE_OPTION',  (bool) apply_filters('wp-content-translator/option/translate_numeric_option'    , false));
        define('WTC_UNTRANSLATEBLE_SITE_OPTION', (array) apply_filters('wp-content-translator/option/untranslatable_options', array()));
        define('WTC_TRANSLATABLE_SITE_OPTION', (array) apply_filters('wp-content-translator/option/translatable_options', array()));

        define('WCT_TRANSLATE_OPTION',          (bool) apply_filters('wp-content-translator/option/translate_option'            , true));
        define('WCT_TRANSLATE_NUMERIC_OPTION',  (bool) apply_filters('wp-content-translator/option/translate_numeric_option'    , false));
        define('WTC_TRANSLATE_HIDDEN_OPTION',   (bool) apply_filters('wp-content-translator/option/translate_hidden_option'     , false));

    }

    public function userConfiguration() // : void - Waiting for 7.1 enviroments to "be out there".
    {
        //All options exept this will be inherited from meta
        define('WCT_TRANSLATE_USER',            (bool) apply_filters('wp-content-translator/option/translate_user'            , true));
    }

    public function commentConfiguration() // : void - Waiting for 7.1 enviroments to "be out there".
    {
        define('WCT_TRANSLATE_COMMENT',         (bool) apply_filters('wp-content-translator/option/translate_comment'         , true));
    }

}
