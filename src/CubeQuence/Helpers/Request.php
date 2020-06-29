<?php

namespace CQ\Helpers;

class Request
{
    /**
     * Check if request contains isJSON.
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
}
