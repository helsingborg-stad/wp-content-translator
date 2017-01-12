<?php

namespace ContentTranslator;

class Post
{
    public function __construct()
    {
        if (isset(\ContentTranslator\Switcher::$currentLanguage) && !is_null(\ContentTranslator\Switcher::$currentLanguage) && !empty(\ContentTranslator\Switcher::$currentLanguage)) {
            add_action('admin_enqueue_scripts', function () {
                global $post;
                $post = $this->get($post);
            });

            add_filter('posts_results', array($this, 'postsResults'));
        }
    }

    /**
     * Translates a post object
     * @param  \WP_Post   $post         Post object
     * @param  array|null $translations Array with translations
     * @return \WP_Post                 Translated post object
     */
    protected function get(\WP_Post $post, $translations = null) : \WP_Post
    {
        if (is_null($translations)) {
            $translations = $this->getTranslationPosts(array($post->ID));
        }

        if (empty($translations)) {
            return $post;
        }

        $post->post_title = $translations[$post->ID]->post_title;
        $post->post_content = $translations[$post->ID]->post_content;

        return $post;
    }

    /**
     * Gets the translations from the db
     * @param  array  $posts Post ids to get
     * @return array
     */
    public function getTranslationPosts(array $posts) : array
    {
        global $wpdb;

        $table = $wpdb->posts . '_' . \ContentTranslator\Switcher::$currentLanguage->code;
        $postIds = implode(',', $posts);

        $results = $wpdb->get_results("SELECT * FROM $table WHERE ID IN ($postIds)");

        if (empty($results)) {
            return array();
        }

        $translations = array();
        foreach ($results as $translation) {
            $translations[$translation->ID] = $translation;
        }

        return $translations;
    }

    /**
     * Translates posts_results
     * @param  array $posts Posts in result
     * @return array
     */
    public function postsResults($posts) : array
    {
        global $wpdb;

        $postIds = array();
        foreach ($posts as $post) {
            $postIds[] = $post->ID;
        }

        $translations = $this->getTranslationPosts($postIds);

        foreach ($posts as $post) {
            $post = $this->get($post, $translations);
        }

        return $posts;
    }
}
