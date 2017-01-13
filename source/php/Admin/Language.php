<?php

namespace ContentTranslator\Admin;

class Language
{
    public function __construct()
    {
        add_action('admin_bar_menu', array($this, 'addSwitcher'));    }

    public function addSwitcher()
    {
        do_action('wp-content-translator/before_add_language');

        global $wp_admin_bar;

        $wp_admin_bar->add_node( array(
            'id' => 'language_links',
            'title' => __('Language'),
            'href' => "://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'],
        ));

        //Languages
        foreach (\ContentTranslator\Language::installed() as $installedLanguage) {
            $wp_admin_bar->add_node( array(
                'parent' => 'language_links',
                'id' => 'language_links_'. $installedLanguage->code,
                'title' => $installedLanguage->name . "(". $installedLanguage->nativeName. ")",
                'href' => "https://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'].http_build_query(array_merge($_GET,array('lang' => $installedLanguage->code))),
            ));
        }

        do_action('wp-content-translator/after_add_language');
    }
}
