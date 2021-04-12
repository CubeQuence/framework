<?php

declare(strict_types=1);

namespace CQ\Helpers;

final class StrHelper
{
    /**
     * Escape a string.
     */
    public static function escape(string $string): string
    {
        $string = trim(str: $string);
        $string = stripslashes(str: $string);

        return htmlspecialchars(string: $string);
    }
}
