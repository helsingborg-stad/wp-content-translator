<?php

namespace ContentTranslator\Admin;

class Options
{
    public static $optionKey = array(
        'installed' => 'wp-content-translator-installed',
        'active' => 'wp-content-translator-active',
        'comments' => 'wp-content-translator-comments-connect'
    );

    public function __construct()
    {
        add_action('admin_menu', array($this, 'addOptionsPage'));
        add_action('admin_init', array($this, 'saveOptions'));
        add_action('admin_init', array($this, 'removeLang'));
    }

    public function addOptionsPage()
    {
        do_action('wp-content-translator/before_add_options_page');

        add_menu_page(
            __('Languages', 'wp-content-translator'),
            __('Languages', 'wp-content-translator'),
            'edit_posts',
            'languages',
            function () {
                $defaultLang = \ContentTranslator\Language::default();
                $installed = \ContentTranslator\Language::installed(false);
                $allInstalled = \ContentTranslator\Language::installed(true);
                $commentConnect = get_option(self::$optionKey['comments'], array());

                include WPCONTENTTRANSLATOR_TEMPLATE_PATH . 'admin/options.php';
            },
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIxLjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyNTUgMTQzIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyNTUgMTQzOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTE3MS40LDQuMWgzMS44bDQ3LjYsMTM0LjdoLTMwLjVsLTguOS0yNy43aC00OS42bC05LjEsMjcuN2gtMjkuNEwxNzEuNCw0LjF6IE0xNjkuNSw4Ny45SDIwNGwtMTctNTNMMTY5LjUsODcuOXoiLz4KPC9nPgo8cmVjdCB4PSIzNC42IiB5PSI0LjEiIHdpZHRoPSI5Ni4zIiBoZWlnaHQ9IjE5LjMiLz4KPGc+Cgk8cGF0aCBkPSJNMTExLDEzOC45bDYuOS0xOS4zaC0xNS44VjYxLjloMzYuNGw2LjktMTkuM2gtMTMwdjE5LjNoMzguNGMtMC45LDIwLjctNy4xLDUwLjgtNDkuNiw1Ny45bDMuMiwxOQoJCWM1Ni45LTkuNSw2NC42LTUzLjEsNjUuNy03Ni45aDkuOHY3Ny4xSDExMXoiLz4KPC9nPgo8L3N2Zz4K',
            100
        );

        do_action('wp-content-translator/after_add_options_page');
    }

    /**
     * Handle remove language
     * @return void
     */
    public function removeLang()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wp-content-translator-remove-lang')) {
            return;
        }

        $langCode = sanitize_text_field($_REQUEST['id']);
        $lang = new \ContentTranslator\Language($langCode, false);
        $lang->uninstall();

        return;
    }

    /**
     * Saves the options on the options page
     * @return void
     */
    public function saveOptions()
    {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wp-content-translator-options')) {
            return;
        }

        if (isset($_POST['newlang']) && is_array($_POST['newlang'])) {
            foreach ($_POST['newlang'] as $lang) {
                new \ContentTranslator\Language($lang['lang']);
            }
        }

        $this->setActiveLanguages(isset($_POST['active-languages']) ? $_POST['active-languages'] : array());
        $this->setCommentsLoading(isset($_POST['comments']) ? $_POST['comments'] : array());
    }

    public function setCommentsLoading($data)
    {
        $commentsData = array();

        foreach ($data as $key => $languages) {
            $commentsData[$key] = array_keys($languages);
        }

        if (empty($commentsData)) {
            return delete_option(self::$optionKey['comments']);
        }

        return update_option(self::$optionKey['comments'], $commentsData);
    }

    /**
     * Sets the list of activated languages
     * @param array $languages Activated languages
     */
    public function setActiveLanguages(array $active) : bool
    {
        return update_option(self::$optionKey['active'], $active);
    }
}
