<?php

$config = (object) array(
    'translate' => true,
    'translate_hidden' => false,
    'translate_numeric' => false,
    'untranslatable' => array(
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
    'translatable' => array(
        '_aioseop_title',
        '_aioseop_description'
    )
);

return (object) apply_filters('wp-content-translator/configuration/meta', $config);
