<?php

namespace ContentTranslator\Translate;

class Comment
{
    public function __construct()
    {
        if (WCT_TRANSLATE_COMMENT) {

        }
    }

     /**
     * Install procedure
     * @param  string $language Language to install
     * @return bool
     */
    public static function install(string $language) : bool {
        global $wpdb;
        \ContentTranslator\Helper\Database::duplicateTable($wpdb->comments, self::getTableName($language));
        return true;
    }

    /**
     * Is installed?
     * @param  string  $language Language to check
     * @return boolean
     */
    public static function isInstalled(string $language) : bool
    {
        if (!\ContentTranslator\Helper\Database::tableExist(self::getTableName($language))) {
            return false;
        }

        return true;
    }

    /**
     * Uninstall procedure
     * @param  string $language Language to uninstall
     * @return bool
     */
    public static function uninstall(string $language) : bool {
        if (apply_filters('wp-content-translator/should_drop_table_when_uninstalling_language', true)) {
            Helper\Database::dropTable(self::getTableName($language));
        }

        return true;
    }

    /**
     * Gets table name for posts of the specific language
     * @param  string $language Language
     * @return string           Table name
     */
    public static function getTableName(string $language) : string {
        global $wpdb;
        return strtolower($wpdb->comments . '_' . $language);
    }
}
