<?php

$config = (object) array(
    'translate_user' => true,
    'translate_hidden_meta' => false,
    'translate_numeric_meta' => false,
    'untranslatable_meta' => array(
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
        'metaboxhidden_post'
    ),
    'translatable_meta' => array()
);

return (object) apply_filters('wp-content-translator/configuration/user', $config);
