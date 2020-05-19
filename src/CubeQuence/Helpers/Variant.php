<?php

namespace CQ\Helpers;

use CQ\Config\Config;

class Variant // TODO: build variant helper
{
    public static function check($variant, $type, $value)
    {
        $variants = Config::get('variants');

        return true;
    }
}
