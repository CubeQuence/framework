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
}
