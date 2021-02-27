<?php

declare(strict_types=1);

namespace CQ\Helpers;

class State
{
    /**
     * Set state.
     *
     * @param string $custom optional
     */
    public static function set(string $custom = ''): string
    {
        $state = $custom ? $custom : Str::random();

        return Session::set(
            name: 'state',
            data: $state
        );
    }

    /**
     * Validate $provided_state.
     *
     * @param bool   $unset_state optional
     */
    public static function valid(string $provided_state, bool $unset_state = true): bool
    {
        $known_state = Session::get(name: 'state');

        if ($unset_state) {
            Session::unset(name: 'state');
        }

        if (!$provided_state) {
            return false;
        }

        return $provided_state === $known_state;
    }
}
