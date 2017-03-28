<?php

namespace ContentTranslator\Translate;

class Meta extends \ContentTranslator\Entity\Translate
{

    private static $metaType;
    private $allowedTypes = array('post','user','comment');
    private $metaConfiguration;

    /**
     * Constructor
     * @param  string $metaType The metadata type that should be handled. Valid: post, user.
     * @return void
     */
    public function __construct($metaType = 'post')
    {
        parent::__construct();

        if (in_array($metaType, $this->allowedTypes)) {
            self::$metaType = $metaType;

            if ($metaType == 'user') {
                $this->metaConfiguration = $this->configuration->usermeta;
            } else {
                $this->metaConfiguration = $this->configuration->meta;
            }
        } else {
            throw new \Exception("An incorrent meta-type was provided to meta translation.", 1);
        }

        if (
            ($this->configuration->meta->translate && $metaType == 'post') ||
            ($this->configuration->usermeta->translate && $metaType == 'user') ||
            ($this->configuration->comment->translate && $metaType == 'comment')
        ) {
            add_filter('get_'. self::$metaType .'_metadata', array($this, 'get'), 1, 4);
            add_filter('update_'. self::$metaType .'_metadata', array($this, 'save'), 1000, 4);
            add_filter('add_'. self::$metaType .'_metadata', array($this, 'save'), 1000, 4);
        }
    }

    /**
     * Install procedure
     * @param  string $language Language to install
     * @return bool
     */
    public static function install(string $language) : bool
    {
        do_action('wp-content-translator/' . self::$metaType . '/install', $language);
        return true;
    }

    /**
     * Is installed?
     * @param  string  $language Language to check
     * @return boolean
     */
    public static function isInstalled(string $language) : bool
    {
        return apply_filters('wp-content-translator/' . self::$metaType . '/is_installed', true, $language);
    }

    /**
     * Uninstall procedure
     * Removes meta of the removed language
     * @param  string $language Language to install
     * @return bool]
     */
    public static function uninstall(string $language) : bool
    {
        do_action('wp-content-translator/' . self::$metaType . '/uninstall', $language);

        // Bail if we should not remove the meta
        if (!apply_filters('wp-content-translator/' . self::$metaType . '/remove_when_uninstalling', true)) {
            return false;
        }

        $this->db->query(
            $wpdb->prepare("DELETE FROM {$this->db->postmeta} WHERE meta_key LIKE %s", '%_' . $language)
        );

        return true;
    }

    public function save($null, int $post_id, string $meta_key, $meta_value) // : ?bool  - Waiting for 7.1 enviroments to "be out there".
    {
        if (!$this->isLangual($meta_key) && $this->shouldTranslate($meta_key, $meta_value)) {
            //Create meta key
            $langual_meta_key = $this->createLangualKey($meta_key);
            $identical = $this->identicalToBaseLang($meta_key, $meta_value, $post_id);

            $meta_table = self::$metaType . 'meta';
            $meta_table = $this->db->$meta_table;

            $metaIdColumn = 'meta_id';
            switch (self::$metaType) {
                case 'user':
                    $metaIdColumn = 'umeta_id';
                    break;
            }

            //Update post meta
            if (!$identical) {
                $meta_id = $this->db->get_var($this->db->prepare(
                    "SELECT {$metaIdColumn} FROM {$meta_table} WHERE post_id = %d AND meta_key = %s",
                    array(
                        $post_id,
                        $langual_meta_key
                    )
                ));

                if ($meta_id) {
                    // Update existing meta
                    $this->db->update(
                        $meta_table,
                        array(
                            'meta_value' => $meta_value
                        ),
                        array(
                            $metaIdColumn => $meta_id
                        ),
                        array(
                            '%s'
                        ),
                        array(
                            '%d'
                        )
                    );
                } else {
                    // Create new meta
                    $this->db->insert(
                        $meta_table,
                        array(
                            self::$metaType . '_id' => $post_id,
                            'meta_key' => $langual_meta_key,
                            'meta_value' => $meta_value
                        ),
                        array(
                            '%s'
                        )
                    );
                }

                return false;
            }

            //Clean meta that equals base language
            if ($identical) {
                delete_post_meta($post_id, $langual_meta_key);
                return false;
            }
        }

        return null;
    }

    public function get($type, int $post_id, string $meta_key, bool $single) // : ?string - Waiting for 7.1 enviroments to "be out there".
    {
        if (!$this->isLangual($meta_key) && $this->shouldTranslate($meta_key)) {
            remove_filter('get_'. self::$metaType .'_metadata', array($this, 'get'), 1);
            $translation = get_post_meta($post_id, $this->createLangualKey($meta_key));
            add_filter('get_'. self::$metaType .'_metadata', array($this, 'get'), 1, 4);

            if (!$this->configuration->general->translate_fallback && implode("", $translation) == "") {
                return "";              // Abort and return empty (no fallback)
            } elseif ($this->configuration->general->translate_fallback && implode("", $translation) == "") {
                return null;            // Continiue normal procedure (fallback to base lang)
            } else {
                return $translation;    // Translation found, return value
            }
        }

        return null;
    }

    private function shouldTranslate(string $meta_key, $meta_value = null) : bool
    {
        if (!is_null($meta_value) && empty($meta_value)) {
            return false;
        }

        if (in_array($meta_key, $this->metaConfiguration->untranslatable)) {
            return false;
        }

        if (in_array($meta_key, $this->metaConfiguration->translatable)) {
            return true;
        }

        if (!$this->metaConfiguration->translate_hidden && substr($meta_key, 0, 1) == "_") {
            return false;
        }

        if (!$this->metaConfiguration->translate_numeric && is_numeric($meta_value) && $meta_value != null) {
            return false;
        }

        return apply_filters('wp-content-translator/' . self::$metaType . '/should_translate_default', true, $meta_key);
    }

    private function identicalToBaseLang(string $meta_key, $translated, int $post_id) : bool
    {
        remove_filter('get_'. self::$metaType .'_metadata', array($this, 'get'), 1);
        $default = get_post_meta($post_id, $meta_key, true);
        add_filter('get_'. self::$metaType .'_metadata', array($this, 'get'), 1, 4);

        if (trim($default) == trim($translated)) {
            return true;
        }

        return false;
    }
}
