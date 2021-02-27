<?php

declare(strict_types=1);

namespace CQ\Helpers;

class IP
{
    /**
     * Check if IP is in range (only supports IPv4)
     */
    public static function inRange(string $range, string $ip): bool
    {
        if (!str_contains(
            haystack: $range,
            needle: '/'
        )) {
            $range .= '/32';
        }

        [$range, $netmask] = explode(
            delimiter: '/',
            string: $range,
            limit: 2
        );

        $range_decimal = ip2long(ip_address: $range);
        $ip_decimal = ip2long(ip_address: $ip);

        $wildcard_decimal = pow(
            base: 2,
            exp: 32 - $netmask
        ) - 1;
        $netmask_decimal = ~$wildcard_decimal;

        return ($ip_decimal & $netmask_decimal) === ($range_decimal & $netmask_decimal);
    }
}
