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

        // Setup wpdb
        global $wpdb;
        self::$defaultWpdbTables = array(
            'posts' => $wpdb->posts
        );

        // Helpers
        new Helper\Database();
        new WpApi();

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

        foreach ((array) $languages as $lang) {
            if (!empty($lang) && isset($lang->code)) {
                new \ContentTranslator\Language($lang->code);
            }
        }
    }

    public function adminEnqueue() // : void - Waiting for 7.1 enviroments to "be out there".
    {
        wp_enqueue_style('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/css/wp-content-translator-admin.min.css', null, '1.0.0');
        wp_enqueue_script('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/js/wp-content-translator-admin.dev.js', array('jquery'), '1.0.0', true);
    }
}
