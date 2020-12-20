<?php

namespace CQ\Helpers;

use CQ\Helpers\IP;

class Request
{
    /**
     * Check if request is JSON.
     *
     * @param object $request
     *
     * @return bool
     */
    public static function isJSON($request)
    {
        return Str::contains($request->getHeader('content-type')[0], '/json');
    }

    /**
     * Check if request is Form.
     *
     * @param object $request
     *
     * @return bool
     */
    public static function isForm($request)
    {
        return $request->getHeader('content-type')[0] == 'application/x-www-form-urlencoded';
    }

    /**
     * Get request path.
     *
     * @param object $request
     *
     * @return string
     */
    public static function path($request)
    {
        $path = $request->getUri();
        $path = strtok($path, '?');

        return strtok($path, '#');
    }

    /**
     * Get user ip.
     *
     * @return string
     */
    public static function ip()
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
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
                if (IP::inRange($range, $_SERVER['REMOTE_ADDR'])) {
                    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
                    break;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get user_agent.
     *
     * @return string
     */
    public static function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Get origin.
     *
     * @return string
     */
    public static function origin()
    {
        return $_SERVER['HTTP_ORIGIN'];
    }
}
