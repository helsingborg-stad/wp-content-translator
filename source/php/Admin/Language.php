<?php

namespace ContentTranslator\Admin;

class Language
{
    public function __construct()
    {
        add_action('admin_bar_menu', array($this, 'addSwitcher'), 999);    }

    public function addSwitcher()
    {
        do_action('wp-content-translator/before_add_admin_menu_item');

        global $wp_admin_bar;

        if (!empty(\ContentTranslator\Language::installed())) {

            $wp_admin_bar->add_node( array(
                'id' => 'language_links',
                'title' => __('Language', 'wp-content-translator'),
                'href' => admin_url('admin.php?page=languages')
            ));

            //Remove current lang
            $get_var = $_GET;
            if (isset($get_var['lang'])) {
                unset($get_var['lang']);
            }

            foreach (\ContentTranslator\Language::installed() as $installedLanguage) {
                $wp_admin_bar->add_node( array(
                    'parent' => 'language_links',
                    'id' => 'language_links_'. $installedLanguage->code,
                    'title' => $installedLanguage->name,
                    'href' => "http" . ( is_ssl() ? 's' : '' ) . "://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'] . http_build_query(array_merge($get_var, array('lang' => $installedLanguage->code))),
                ));
            }

        } else {
            $wp_admin_bar->add_node( array(
                'id' => 'language_links',
                'title' => __('Setup Languages', 'wp-content-translator'),
                'href' => admin_url('admin.php?page=languages')
            ));
        }

        do_action('wp-content-translator/after_add_admin_menu_item');
    }
}
