<?php

namespace ContentTranslator;

class WpApi
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'endpoints'));
    }

    public function endpoints()
    {
        register_rest_route('content-translator/v1', 'active', array(
            'methods' => 'GET',
            'callback' => '\ContentTranslator\Language::active',
        ));

        register_rest_route('content-translator/v1', 'default', array(
            'methods' => 'GET',
            'callback' => '\ContentTranslator\Language::default',
        ));
    }
}
