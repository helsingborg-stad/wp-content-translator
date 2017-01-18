<?php

namespace ContentTranslator\Translate;

class Comment extends \ContentTranslator\Entity\Translate
{
    const META_LANG_KEY = 'wp-content-translate-comment-language';

    public function __construct()
    {
        parent::__construct();

        if ($this->configuration->comment->translate) {
            add_action('comment_post', array($this, 'save'), 10, 3);
            add_action('pre_get_comments', array($this, 'get'));
        }
    }

     /**
     * Install procedure
     * @param  string $language Language to install
     * @return bool
     */
    public static function install(string $language) : bool {
        return true;
    }

    /**
     * Is installed?
     * @param  string  $language Language to check
     * @return boolean
     */
    public static function isInstalled(string $language) : bool
    {
        return true;
    }

    /**
     * Uninstall procedure
     * @param  string $language Language to uninstall
     * @return bool
     */
    public static function uninstall(string $language) : bool {
        return true;
    }

    /**
     * Modifies the comment query
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function get($query)
    {
        $connections = $this->getConnections();
        $connections[] = \ContentTranslator\Switcher::$currentLanguage->code;

        $metaQuery = array(
            'relation' => 'OR'
        );

        foreach ($connections as $connection) {
            $metaQuery[] = array(
                'key' => self::META_LANG_KEY,
                'value' => $connection,
                'compare' => '='
            );

            // Make the default language include comment without language meta
            if (\ContentTranslator\Language::isDefault($connection)) {
                $metaQuery[] = array(
                    'key' => self::META_LANG_KEY,
                    'compare' => 'NOT EXISTS'
                );
            }
        }

        $query->query_vars['meta_query'] = array_merge((array)$query->query_vars['meta_query'], $metaQuery);
        return $query;
    }

    /**
     * Add language meta when saving comment
     * @param  int         $id          Comment id
     * @param  int|string  $approved    Approved or not
     * @param  array       $data        Comment data
     * @return void
     */
    public function save(int $id, $approved, array $data)
    {
        $language = \ContentTranslator\Switcher::$currentLanguage->code;
        update_comment_meta($id, self::META_LANG_KEY, $language);
    }

    /**
     * Get connection options (from options page)
     * @param  string|null $lang Language to get
     * @return array
     */
    public function getConnections(string $lang = null) : array
    {
        if (is_null($lang)) {
            $lang = \ContentTranslator\Switcher::$currentLanguage->code;
        }

        $connections = get_option(\ContentTranslator\Admin\Options::$optionKey['comments'], array());

        if (isset($connections[$lang])) {
            return apply_filters('wp-content-translator/comment/connections', $connections[$lang], $lang);
        }

        return apply_filters('wp-content-translator/comment/connections', array(), $lang);
    }
}
