<?php

$config = (object) array(
    'translate_fallback' => true,
    'translate_delimeter' => "_"
);

return (object) apply_filters('wp-content-translator/configuration/general', $config);
