<?php

namespace ContentTranslator;

class Language
{
    public static $all;
    public static $defaultLocale;

    protected $db;

    private $components = array(
        '\ContentTranslator\Translate\Post',
        '\ContentTranslator\Translate\Meta',
        '\ContentTranslator\Translate\Option',
        '\ContentTranslator\Translate\SiteOption',
        '\ContentTranslator\Translate\User',
        '\ContentTranslator\Translate\Comment',
        '\ContentTranslator\Translate\CommentMeta'
    );

    private $code;
    private $language;
    private $tables;

    public function __construct(string $code, bool $install = true)
    {
        global $wpdb;
        $this->db = $wpdb;

        $this->code = $code;
        $this->language = self::find($code);

        self::$defaultLocale = get_locale();

        // Should we install (ie create tables and such) the language
        if ($install && !$this->isInstalled()) {
            $this->install();
        }
    }

    /**
     * Get table names for current language
     * @param  string $table Table to get (posts|postmeta)
     * @param  string $lang  Language code
     * @return array         Table
     */
    public static function getTable(string $table = null, string $lang = null) : array
    {
        $suffix = '';
        if (!self::isDefault() && \ContentTranslator\Switcher::isLanguageSet()) {
            $suffix = '_' . strtolower(\ContentTranslator\Switcher::$currentLanguage->code);
        }

        $tables = array(
            'posts' => array(
                'name' => \ContentTranslator\App::$defaultWpdbTables['posts'] . $suffix,
                'auto_increment' => 'ID'
            )
        );

        if (is_null($table)) {
            return $tables;
        }

        return isset($tables[$table]) ? $tables[$table] : array();
    }

    /**
     * Installs the language if needed
     * @return boolean
     */
    public function install() : bool
    {
        do_action('wp-content-translator/before_install_language', $this->code, $this);

        $toInstall = $this->checkInstallation('uninstalled');

        // Install components
        foreach ($toInstall as $component) {
            if (!method_exists($component, 'install')) {
                // Method does not exist, continue
                continue;
            }

            $component::install($this->code);
        }

        // Download WP language pack for the language
        $download = apply_filters('wp-content-translator/should_download_wp_translation_when_installing', true, $this->code, $this);
        if ($download && !in_array($this->code, get_available_languages())) {
            $download = @wp_download_language_pack($this->code);
        }

        // Add to list of installed languages
        $installed = get_option(Admin\Options::$optionKey['installed'], array());
        $installed[] = $this->code;
        $installed = array_unique($installed);

        update_option(Admin\Options::$optionKey['installed'], $installed);

        do_action('wp-content-translator/after_install_language', $this->code, $this);

        return true;
    }

    public function uninstall() : bool
    {
        do_action('wp-content-translator/before_uninstall_language', $this->code, $this);

        // Uninstall components
        foreach ($this->components as $component) {
            if (!method_exists($component, 'uninstall')) {
                // Method does not exist, continue
                continue;
            }

            $component::uninstall($this->code);
        }

        // Remove from activated
        $active = get_option(Admin\Options::$optionKey['active'], array());

        if (array_search($this->code, $active) !== false) {
            $index = array_search($this->code, $active);
            unset($active[$index]);
        }

        update_option(Admin\Options::$optionKey['active'], $active);

        // Remove from installed
        $installed = get_option(Admin\Options::$optionKey['installed'], array());
        if (array_search($this->code, $installed) !== false) {
            $index = array_search($this->code, $installed);
            unset($installed[$index]);
        }

        update_option(Admin\Options::$optionKey['installed'], $installed);

        do_action('wp-content-translator/after_uninstall_language', $this->code, $this);

        wp_redirect(apply_filters('wp-content-translator/redirect_after_uninstall_language', $_SERVER['HTTP_REFERER'], $this->code, $this));
        exit;
    }

    /**
     * Check if tables is installed
     * @return boolean
     */
    public function isInstalled() : bool
    {
        $check = $this->checkInstallation();
        return empty($check['uninstalled']);
    }

    /**
     * Checks that the install is complete
     * @return array Installed and uninstalled components
     */
    public function checkInstallation(string $key = null) : array
    {
        $installed = array();
        $uninstalled = array();

        foreach ($this->components as $component) {
            if (!method_exists($component, 'isInstalled')) {
                // Method does not exist, continue
                continue;
            }

            if ($component::isInstalled($this->code)) {
                $installed[] = $component;
            } else {
                $uninstalled[] = $component;
            }
        }

        $installation = array(
            'installed' => $installed,
            'uninstalled' => $uninstalled
        );

        if (is_string($key) && isset($installation[$key])) {
            return $installation[$key];
        }

        return $installation;
    }

    /**
     * Get the default language (from wp settings)
     * @return stdClass Language
     */
    public static function default() : \stdClass
    {
        $locale = null;

        if (!self::$defaultLocale) {
            return self::find(get_locale());
        }

        return self::find(self::$defaultLocale);
    }

    /**
     * Check if current language or given langauge is the default language
     * @param  string|null $code Langauge code
     * @return boolean
     */
    public static function isDefault(string $code = null)
    {
        if (is_null($code)) {
            if (!isset(\ContentTranslator\Switcher::$currentLanguage->code)) {
                return false;
            }

            $code = \ContentTranslator\Switcher::$currentLanguage->code;
        }

        $default = self::default();
        return $default->code === $code;
    }

    /**
     * Gets all languages
     * @return array Languages
     */
    public static function all()
    {
        if (isset(self::$all) && !empty(self::$all)) {
            return self::$all;
        }

        $languages = array();

        require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
        $translations = json_decode(json_encode(wp_get_available_translations()));

        foreach ($translations as $key => $translation) {
            $languages[$key] = array(
                'code' => $translation->language,
                'name' => self::defaultLanguage($translation->english_name),
                'nativeName' => $translation->native_name
            );
        }

        uasort($languages, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $languages = json_decode(json_encode($languages));

        self::$all = $languages;
        return self::$all;
    }

    /**
     * Find a specific language by language code
     * @param  string $key Language code
     * @return stdClass    The language's info
     */
    public static function find(string $key) : \stdClass
    {
        $all = self::all();

        if (isset($all->$key)) {
            return $all->$key;
        }

        return new \stdClass();
    }

    /**
     * Get languages in use (both active and inactive)
     * @return array Languages
     */
    public static function installed($includeDefault = true) : array
    {
        $keys = get_option(Admin\Options::$optionKey['installed'], array());
        $installed = array();

        $default = self::default();

        if ($includeDefault) {
            $installed[$default->code] = $default;
        } else {
            if ($index = array_search($default->code, $keys)) {
                unset($keys[$index]);
            }
        }

        foreach ($keys as $key) {
            $search = self::find($key);

            if ($search) {
                $installed[$key] = $search;
            }
        }

        return $installed;
    }

    /**
     * Get unused languages
     * @return array
     */
    public static function uninstalled() : array
    {
        $defaultLang = self::default();

        $uninstalled = array_diff_key((array)self::all(), self::installed());
        if (isset($uninstalled[$defaultLang->code])) {
            unset($uninstalled[$defaultLang->code]);
        }

        return $uninstalled;
    }

    /**
     * Get active languages in use
     * @return array Languages
     */
    public static function active() : array
    {
        $keys = get_option(Admin\Options::$optionKey['active'], array());
        $active = array();

        // Add default lang to active
        $default = self::default();
        $active[$default->code] = $default;

        foreach ($keys as $key) {
            $search = self::find($key);

            if ($search) {
                $active[$key] = $search;
            }
        }

        return $active;
    }

    /**
     * Checks if a language is activated
     * @param  string  $code Langage code
     * @return boolean
     */
    public static function isActive(string $code) : bool
    {
        $active = self::active();

        if (isset($active[$code])) {
            return true;
        }

        return false;
    }

    /**
     * Gives unnamed langages a name.
     * @param  string  $lang Langage name
     * @return string
     */
    public static function defaultLanguage(string $lang) : string
    {
        if (empty($lang)) {
            $lang = __("Undefined Language", 'wp-content-translator');
        }

        return $lang;
    }
}
