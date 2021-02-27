<?php

declare(strict_types=1);

namespace CQ\Helpers;

class Request
{
    /**
     * Check if request is JSON.
     *
     * @param object $request // TODO: set correct ServerRequest data type
     */
    public static function getHeader(object $request, string $name): string
    {
        return $request->getHeaderLine($name);
    }

    /**
     * Check if request is JSON.
     */
    public static function isJSON(object $request): bool
    {
        return str_contains(
            haystack: self::getHeader(
                request: $request,
                name: 'content-type'
            ),
            needle: '/json'
        );
    }

    /**
     * Check if request is Form.
     */
    public static function isForm(object $request): bool
    {
        return self::getHeader(
            request: $request,
            name: 'content-type'
        ) === 'application/x-www-form-urlencoded';
    }

    /**
     * Get request path.
     */
    // TODO: use new request method $route->path() or smth
    public static function path(object $request): string
    {
        $path = $request->getUri();
        $path = strtok(str: $path, token: '?');

        return strtok(str: $path, token: '#');
    }

    /**
     * Get user ip.
     */
    public static function ip(): string
    {
        $server_ip = $_SERVER['REMOTE_ADDR'];
        $cloudflare_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];

        if (!$cloudflare_ip) {
            return $server_ip;
        }

        $cf_ip_ranges = [
                '173.245.48.0/20',
                '103.21.244.0/22',
                '103.22.200.0/22',
                '103.31.4.0/22',
                '141.101.64.0/18',
                '108.162.192.0/18',
                '190.93.240.0/20',
                '188.114.96.0/20',
                '197.234.240.0/22',
                '198.41.128.0/17',
                '162.158.0.0/15',
                '104.16.0.0/12',
                '172.64.0.0/13',
                '131.0.72.0/22',
            ];

        foreach ($cf_ip_ranges as $range) {
            if (IP::inRange(range: $range, ip: $server_ip)) {
                return $cloudflare_ip;

                break;
            }
        }
    }
}
