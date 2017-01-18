<?php

$config = (object) array(
    'translate_meta' => true,
    'translate_hidden_meta' => false,
    'translate_numeric_meta' => false,
    'untranslatable_meta' => array(
        '_edit_lock',
        '_edit_last',
        '_wp_page_template',
        'nickname',
        'first_name',
        'last_name',
        'rich_editing',
        'comment_shortcuts',
        'admin_color',
        'show_admin_bar_front',
        'show_welcome_panel',
        'session_tokens',
        'closedpostboxes_page',
        'metaboxhidden_page',
        'closedpostboxes_post',
        'metaboxhidden_post',
        'modularity-modules',
        'modularity-sidebar-options'
    ),
    'translatable_meta' => array(
        '_aioseop_title',
        '_aioseop_description'
    )
);

return (object) apply_filters('wp-content-translator/configuration/meta', $config);
