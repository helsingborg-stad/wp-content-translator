<?php

namespace ContentTranslator;

class App
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));

        new Admin\Options();
    }

    public function adminEnqueue()
    {
        wp_enqueue_style('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/css/wp-content-translator-admin.min.css', null, '1.0.0');
        wp_enqueue_script('wp-content-translator-admin', WPCONTENTTRANSLATOR_URL . '/dist/js/wp-content-translator-admin.dev.js', array('jquery'), '1.0.0', true);
    }
}
