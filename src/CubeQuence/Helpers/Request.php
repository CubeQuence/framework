<?php

namespace CQ\Helpers;

use CQ\Helpers\Str;

class Request
{
    /**
     * Check if request contains isJSON
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
     * Get user ip
     *
     * @return string
     */
    public static function ip()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get user_agent
     *
     * @return string
     */
    public static function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}
