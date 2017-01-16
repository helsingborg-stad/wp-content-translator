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

        // Configuration
        add_action('init', array($this, 'generalConfiguration'));
        add_action('init', array($this, 'metaConfiguration'));
        add_action('init', array($this, 'postConfiguration'));
        add_action('init', array($this, 'optionConfiguration'));

        //
        global $wpdb;
        self::$defaultWpdbTables = array(
            'posts' => $wpdb->posts
        );

        // Core
        new Switcher();
        new Post();
        new Meta();

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
        define('TRANSLATE_DELIMITER', apply_filters('wp-content-translator/option/translate_fallback', "_"));
    }

    public function metaConfiguration () {

        define('TRANSLATE_HIDDEN_META', apply_filters('wp-content-translator/option/translate_hidden_meta', false));

        define('UNTRANSLATEBLE_META', (array) apply_filters('wp-content-translator/option/untranslatable_meta', array(
            'modularity-modules',
            'modularity-sidebar-options'
        )));

        define('TRANSLATABLE_META', (array) apply_filters('wp-content-translator/option/ranslatable_meta', array(
            '_aioseop_title',
            '_aioseop_description',
            '_aioseop_titleatr'
        )));

    }

    public function postConfiguration () {

    }

    public function optionConfiguration () {

    }

}
