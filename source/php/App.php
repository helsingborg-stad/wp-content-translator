<?php

namespace ContentTranslator;

class App
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));

        new Admin\Options();


        add_action('init', function() {
            global $wpdb;


            $lang = "";

            //Avabile
            //var $ms_global_tables = array( 'blogs', 'signups', 'site', 'sitemeta', 'sitecategories', 'registration_log', 'blog_versions' );
            //var $tables = array( 'posts', 'comments', 'links', 'options', 'postmeta','terms', 'term_taxonomy', 'term_relationships', 'termmeta', 'commentmeta' );
            //$global_tables = array( 'users', 'usermeta' );


            //Filter: query add_filter('query', function($sql) {});

            /*var_dump($wpdb->tables);
            $wpdb->tables[0] = "test";
            var_dump($wpdb->tables);*/
        });
    }

    public function adminEnqueue()
    {
        wp_enqueue_style('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/css/wp-content-translator-admin.min.css', null, '1.0.0');
        wp_enqueue_script('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/js/wp-content-translator-admin.dev.js', array('jquery'), '1.0.0', true);
    }
}
