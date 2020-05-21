<?php

namespace CQ\Helpers;

use CQ\Helpers\Str;
use CQ\Helpers\Session;

class State
{
    /**
     * Set state
     * 
     * @param string $custom optional
     * 
     * @return string
     */
    public static function set($custom = '')
    {
        $state = $custom ?: Str::random();

        return Session::set('state', $state);
    }

    /**
     * Validate $provided_state
     *
     * @param string $provided_state
     * @param bool $unset_state optional
     * 
     * @return bool
     */
    public static function valid($provided_state, $unset_state = true)
    {
        $known_state = Session::get('state');

        if ($unset_state) {
            Session::unset('state');
        }

        if (!$provided_state) {
            return false;
        }

        return $provided_state === $known_state;
    }
}
