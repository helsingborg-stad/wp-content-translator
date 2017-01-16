<?php

namespace ContentTranslator\Admin;

class AdminBar
{
    public function __construct()
    {
        add_action('admin_bar_menu', array($this, 'addSwitcher'), 999);
    }

    public function addSwitcher()
    {
        do_action('wp-content-translator/before_add_admin_menu_item');

        global $wp_admin_bar;

        if (!empty(wp_content_translator_languages('installed'))) {

            $wp_admin_bar->add_node( array(
                'id' => 'language_links',
                'title' => __('Language', 'wp-content-translator') . " - " . wp_content_translator_current_language()->name,
                'href' => admin_url('admin.php?page=languages')
            ));

            foreach (wp_content_translator_languages('installed') as $installedLanguage) {
                $wp_admin_bar->add_node( array(
                    'parent' => 'language_links',
                    'id' => 'language_links_'. $installedLanguage->code,
                    'title' => $installedLanguage->name,
                    'href' => $installedLanguage->url,
                    'meta' => array(
                        'class' => $installedLanguage->isCurrent ? 'is-current' : ''
                    )
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
