<?php

declare(strict_types=1);

namespace CQ\Helpers;

use Laminas\Diactoros\ServerRequest;

final class RequestHelper
{
    private array $cloudflareRanges = [ // https://www.cloudflare.com/ips-v4
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
        '172.64.0.0/13',
        '131.0.72.0/22',
        '104.16.0.0/13',
        '104.24.0.0/14',
    ];

    public function __construct(
        private ServerRequest $request
    ) {
    }

    public function getHeader(string $headerName) : string
    {
        return $this->request->getHeaderLine($headerName);
    }

    public function getQueryParam(string $paramName) : string | null
    {
        return $this->request->getQueryParams()[$paramName] ?? null;
    }

    public function isJSON(): bool
    {
        return $this->getHeader('Content-Type') === 'application/json';
    }

    public function isForm(): bool
    {
        return $this->getHeader('Content-Type') === 'application/x-www-form-urlencoded';
    }

    /**
     * Get user ip, works behind custom proxy or CloudFlare
     */
    public function ip(array $proxyRanges = []): string
    {
        $allowedRanges = $proxyRanges ?: $this->cloudflareRanges;

        $remoteIp = $_SERVER['REMOTE_ADDR'];
        $proxyIp = $this->getHeader('X-Forwarded-For');

        if (! $proxyIp) {
            return $remoteIp;
        }

        foreach ($allowedRanges as $range) {
            if ($this->isIpInRange(range: $range, ip: $remoteIp)) {
                return $proxyIp;
            }
        }
    }

    /**
     * Check if IP is in range (only supports IPv4)
     */
    private function isIpInRange(string $range, string $ip): bool
    {
        [$range, $netmask] = explode(
            delimiter: '/',
            string: $range,
            limit: 2
        );

        $rangeDecimal = ip2long(ip_address: $range);
        $ipDecimal = ip2long(ip_address: $ip);

        $wildcardDecimal = pow(
            base: 2,
            exp: 32 - $netmask
        ) - 1;

        $netmask_decimal = ~$wildcardDecimal;

        return ($ipDecimal & $netmask_decimal) === ($rangeDecimal & $netmask_decimal);
    }
}
