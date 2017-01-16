<?php

namespace ContentTranslator;

class Post
{
    public function __construct()
    {
        if (\ContentTranslator\Switcher::isLanguageSet()) {
            add_action('admin_enqueue_scripts', array($this, 'globalPost'));

            add_action('wp', array($this, 'globalPost'));
            add_filter('posts_results', array($this, 'postsResults'));

            add_filter('wp_insert_post_data', array($this, 'save'), 10, 2);
        }
    }

    /**
     * Translates a post object
     * @param  \WP_Post   $post         Post object
     * @param  array|null $translations Array with translations
     * @return \WP_Post                 Translated post object
     */
    protected function get(\WP_Post $post, array $translations = null) : \WP_Post
    {
        if (is_null($translations)) {
            $translations = $this->getTranslationPosts(array($post->ID));
        }

        if (empty($translations) || !isset($translations[$post->ID])) {
            if (!TRANSLATE_FALLBACK) {
                $post->post_title = '';
                $post->post_content = '';
            }

            return $post;
        }

        $post->post_title = $translations[$post->ID]->post_title;
        $post->post_content = $translations[$post->ID]->post_content;

        /**
         * Filter translated post object
         * @param WP_Post $post       The post object
         * @param array $translations Possible translations
         */
        return apply_filters('wp-content-translator/post/get', $post, $translations);
    }

    /**
     * Switches db table so that the post is beeing saved in correct lang-table
     * Inserts or updates (does what needs to be done)
     * @param  array  $data    [description]
     * @param  array  $postarr [description]
     * @return [type]          [description]
     */
    public function save(array $data, array $postarr) : array
    {
        global $wpdb;

        $table = \ContentTranslator\Language::getTable('posts');
        $table = $table['name'];

        $wpdb->posts = $table;

        $existing = false;

        // Check for existing post on the same post id as the one we want to create
        if ($data['post_type'] !== 'revision' && isset($postarr['post_ID']) && !empty($postarr['post_ID'])) {
            $existing = $wpdb->get_row("SELECT ID, post_type FROM $wpdb->posts WHERE ID = " . $postarr['post_ID']);

            // If there's an existing post and it's a revision, update
            // the ID of the revision to a unused ID to be able to syncronize ID
            // between translation post and default post
            if ($existing && $existing->post_type === 'revision') {
                $what = $wpdb->get_row("SHOW TABLE STATUS LIKE '$wpdb->posts'");
                $autoIncrement = $what->Auto_increment + 1;

                $wpdb->update(
                    $wpdb->posts,
                    array('ID' => $autoIncrement),
                    array('ID' => $postarr['post_ID']),
                    array('%d'),
                    array('%d')
                );

                // Reset existing to false since we now
                // have changed the postid of the existing
                // revision post
                $existing = false;
            }
        }

        if (!$existing) {
            remove_filter('wp_insert_post_data', array($this, 'save'), 10);

            // Insert the post
            $insertedPostID = wp_insert_post($data, true);

            // Update the post id to match the original
            if (isset($postarr['post_ID']) && !empty($postarr['post_ID'])) {
                $wpdb->update(
                    $wpdb->posts,
                    // Update
                    array(
                        'ID' => (int) $postarr['post_ID']
                    ),
                    // Where
                    array(
                        'ID' => $insertedPostID
                    ),
                    array('%d'),
                    array('%d')
                );
            }

            add_filter('wp_insert_post_data', array($this, 'save'), 10, 2);
        }

        return $data;
    }

    /**
     * Gets the translations from the db
     * @param  array  $posts Post ids to get
     * @return array
     */
    public function getTranslationPosts(array $posts) : array
    {
        if (empty($posts)) {
            return array();
        }

        global $wpdb;
        $table = \ContentTranslator\Language::getTable('posts');
        $table = $table['name'];

        $postIds = implode(',', $posts);

        $results = $wpdb->get_results("SELECT * FROM $table WHERE ID IN ($postIds) AND post_type != 'revision'");

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
    public function postsResults(array $posts) : array
    {
        global $wpdb;

        $postIds = array();
        foreach ($posts as $post) {
            if ($post->post_type === 'revision') {
                continue;
            }

            $postIds[] = $post->ID;
        }

        $translations = $this->getTranslationPosts($postIds);

        foreach ($posts as $post) {
            $post = $this->get($post, $translations);
        }

        return $posts;
    }

    /**
     * Translate the global WP_Post $post object
     * @return void
     */
    public function globalPost() // : void - Waiting for 7.1 enviroments to "be out there".
    {
        global $post;

        if (is_a($post, 'WP_Post')) {
            $post = $this->get($post);
        }
    }
}
