<?php

namespace ContentTranslator\Admin;

class AdminBar
{

    private $icon;

    public function __construct()
    {
        add_action('admin_bar_menu', array($this, 'addSwitcher'), 999);
    }

    /**
     * Adds language switcher to the admin bar
     * @return  void
     */
    public function addSwitcher()
    {
        do_action('wp-content-translator/before_add_admin_menu_item');

        global $wp_admin_bar;

        if (!empty(wp_content_translator_languages('installed'))) {
            if (is_object(wp_content_translator_current_language())) {
                /**
                 * Filters the name of the current language
                 * @var string
                 */
                $langName = apply_filters('wp-content-translator/admin_bar/current_lang', " - " . wp_content_translator_current_language()->name, wp_content_translator_current_language()->code);

                $translationNodeTitle =  __('Language', 'wp-content-translator') . $langName;
            } else {
                $translationNodeTitle = __('Language', 'wp-content-translator');
            }

            $wp_admin_bar->add_node(array(
                'id' => 'language_links',
                'title' => $translationNodeTitle,
                'href' => admin_url('admin.php?page=languages'),
                'parent' => 'top-secondary'
            ));

            foreach (wp_content_translator_languages('installed') as $installedLanguage) {
                if (!empty($installedLanguage->name)) {
                    $wp_admin_bar->add_node(array(
                        'parent' => 'language_links',
                        'id' => 'language_links_'. $installedLanguage->code,
                        'title' => $installedLanguage->name,
                        'href' => $installedLanguage->url,
                        'meta' => array(
                            'class' => $installedLanguage->isCurrent ? 'is-current' : ''
                        )
                    ));
                }
            }
        } else {
            $wp_admin_bar->add_node(array(
                'id' => 'language_links',
                'title' => __('Setup Languages', 'wp-content-translator'),
                'href' => admin_url('admin.php?page=languages')
            ));
        }

        do_action('wp-content-translator/after_add_admin_menu_item');
    }
}
