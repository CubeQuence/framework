<?php

declare(strict_types=1);

namespace CQ\Helpers;

class Session
{
    /**
     * Set session var.
     *
     * @param mixed  $data
     *
     * @return mixed
     */
    public static function set(string $name, $data)
    {
        $_SESSION[$name] = $data;

        return $data;
    }

    /**
     * Unset session var.
     */
    public static function unset(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * Get session var.
     *
     * @return mixed
     */
    public static function get(string $name)
    {
        return $_SESSION[$name] ?? null;
    }

    /**
     * Destroy and restart a session.
     */
    public static function destroy(): void
    {
        session_destroy();
        session_start();
        session_regenerate_id();
    }
}
