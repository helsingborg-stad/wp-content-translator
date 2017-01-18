<?php

namespace ContentTranslator;

class App
{
    public static $defaultWpdbTables;
    public static $configuration;

    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init()
    {

        // Add scriots & styles for backend.
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));


         /* Should be removed */

        $this->generalConfiguration();
        $this->optionConfiguration();
        $this->commentConfiguration();

        // Setup wpdb
        global $wpdb;
        self::$defaultWpdbTables = array(
            'posts' => $wpdb->posts
        );

        // Helpers
        new Helper\Database();

        // Core
        $this->initLanguages();
        new Switcher();

        // Translate
        if (\ContentTranslator\Switcher::isLanguageSet()) {
            if (!\ContentTranslator\Language::isDefault()) {
                new Translate\Post();
                new Translate\Meta();
                new Translate\Option();
                new Translate\SiteOption();
                new Translate\UserMeta();
                new Translate\CommentMeta();
            }

            new Translate\Comment();
        }

        // Admin
        new Admin\Options();
        new Admin\AdminBar();
    }

    public function initLanguages()
    {
        $languages = \ContentTranslator\Language::installed(false);

        foreach ($languages as $lang) {
            new \ContentTranslator\Language($lang->code);
        }
    }

    public function adminEnqueue() // : void - Waiting for 7.1 enviroments to "be out there".
    {
        wp_enqueue_style('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/css/wp-content-translator-admin.min.css', null, '1.0.0');
        wp_enqueue_script('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/js/wp-content-translator-admin.dev.js', array('jquery'), '1.0.0', true);
    }

    public function generalConfiguration() // : void - Waiting for 7.1 enviroments to "be out there".
    {


        define('WCT_TRANSLATE_FALLBACK', apply_filters('wp-content-translator/option/translate_fallback', true));
        define('WTC_TRANSLATE_DELIMITER', apply_filters('wp-content-translator/option/translate_delimeter', "_"));
    }

    public function optionConfiguration() // : void - Waiting for 7.1 enviroments to "be out there".
    {



        /* REmove this */

        define('WTC_UNTRANSLATEBLE_OPTION', (array) apply_filters('wp-content-translator/option/untranslatable_options', array(
            Admin\Options::$optionKey['active'],
            Admin\Options::$optionKey['installed'],
            Admin\Options::$optionKey['comments'],
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
        )));

        define('WTC_TRANSLATABLE_OPTION', (array) apply_filters('wp-content-translator/option/translatable_options', array(

        )));


        define('WCT_TRANSLATE_OPTION', (bool) apply_filters('wp-content-translator/option/translate_option', true));
        define('WCT_TRANSLATE_NUMERIC_OPTION', (bool) apply_filters('wp-content-translator/option/translate_numeric_option', false));
        define('WTC_TRANSLATE_HIDDEN_OPTION', (bool) apply_filters('wp-content-translator/option/translate_hidden_option', false));
    }


    public function commentConfiguration() // : void - Waiting for 7.1 enviroments to "be out there".
    {

        define('WCT_TRANSLATE_COMMENT', (bool) apply_filters('wp-content-translator/option/translate_comment', true));
        define('WCT_TRANSLATE_COMMENT_META', (bool) apply_filters('wp-content-translator/option/translate_comment_meta', true));
    }
}
