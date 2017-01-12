<?php

namespace ContentTranslator\Entity;

abstract class Translate
{
    abstract protected function get();
    abstract protected function save();
}
