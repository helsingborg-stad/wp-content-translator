<?php

$config = (object) array(
    'translate' => true,
    'translate_meta' => true
);

return (object) apply_filters('wp-content-translator/configuration/comment', $config);
