<?php

$config = (object) array(
    'translate' => true,
    'translate_hidden' => false,
    'translate_numeric' => false,
    'untranslatable' => array(
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
    'translatable' => array()
);

return (object) apply_filters('wp-content-translator/configuration/user', $config);
