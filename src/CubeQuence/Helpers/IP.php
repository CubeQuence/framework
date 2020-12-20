<?php

namespace CQ\Helpers;

use CQ\Helpers\Str;

class IP
{
    /**
     * Check if IP is in range (only supports IPv4)
     *
     * @param string $range
     * @param string $ip
     *
     * @return bool
     */
    public static function inRange($range, $ip)
    {
        if (!Str::contains($range, '/')) {
            $range .= '/32';
        }

        list($range, $netmask) = explode('/', $range, 2);

        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
        $netmask_decimal = ~$wildcard_decimal;

        return (($ip_decimal & $netmask_decimal) === ($range_decimal & $netmask_decimal));
    }
}
