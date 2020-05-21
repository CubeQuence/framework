<?php

namespace CQ\Helpers;

use CQ\Config\Config;

class Variant
{
    /**
     * Check if user license is authorized to execute an action
     *
     * @param string $variant
     * @param string $type
     * @param string|bool $current_value
     * 
     * @return string|int|bool|array
     */
    public static function check($variant, $type, $current_value = null)
    {
        $variant_value = self::variantValue($variant, $type);

        if (is_int($variant) && $current_value) { // If $current_value isn't set return value for type
            return $current_value < $variant_value;
        }

        return $variant_value;
    }

    /**
     * Return value associated with variant and type
     *
     * @param string $variant
     * @param string $type
     * 
     * @return string|int|bool|array
     */
    public static function variantValue($variant, $type)
    {
        return Config::get("variants.{$variant}.{$type}", null);
    }
}
