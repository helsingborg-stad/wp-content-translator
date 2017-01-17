<?php

namespace ContentTranslator\Entity;

class Translate
{
    /**
     * Creates a language specific meta/options key
     * @param  string $key The meta/option key
     * @return string      Langual meta/option key
     */
    protected function createLangualKey(string $key) : string
    {
        return $key . TRANSLATE_DELIMITER . $this->lang;
    }

    /**
     * Check if key is a langual option
     * @param  string  $key Option key
     * @return boolean
     */
    protected function isLangualOption($key)
    {
        return substr($key, -strlen(TRANSLATE_DELIMITER . $this->lang)) == TRANSLATE_DELIMITER . $this->lang ? true : false;
    }
}
