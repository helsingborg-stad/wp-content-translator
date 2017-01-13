<?php

namespace ContentTranslator\Admin;

class Language
{
    public function __construct()
    {
        add_action('admin_bar_menu', array($this, 'addSwitcher'), 999);    }

    public function addSwitcher()
    {
        do_action('wp-content-translator/before_add_language');

        global $wp_admin_bar;

        if (!empty(\ContentTranslator\Language::installed())) {

            $wp_admin_bar->add_node( array(
                'id' => 'language_links',
                'title' => __('Language', 'wp-content-translator')
            ));

            foreach (\ContentTranslator\Language::installed() as $installedLanguage) {
                $wp_admin_bar->add_node( array(
                    'parent' => 'language_links',
                    'id' => 'language_links_'. $installedLanguage->code,
                    'title' => $installedLanguage->name . " (". $installedLanguage->nativeName. ")",
                    'href' => "http" . ( is_ssl() ? 's' : '' ) . "://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'].http_build_query(array_merge($_GET,array('lang' => $installedLanguage->code))),
                ));
            }

        } else {
            $wp_admin_bar->add_node( array(
                'id' => 'language_links',
                'title' => __('Setup Languages', 'wp-content-translator'),
                'href' => admin_url('admin.php?page=languages')
            ));
        }

        do_action('wp-content-translator/after_add_language');
    }
}
