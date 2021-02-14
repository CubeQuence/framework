<?php

namespace CQ\Helpers;

class Session
{
    /**
     * Set session var.
     *
     * @param string $name
     * @param mixed  $data
     *
     * @return mixed
     */
    public static function set($name, $data)
    {
        $_SESSION[$name] = $data;

        return $data;
    }

    /**
     * Unset session var.
     *
     * @param string $name
     */
    public static function unset($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Get session var.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get($name)
    {
        return $_SESSION[$name] ?? null;
    }

    /**
     * Destroy and restart a session.
     */
    public static function destroy()
    {
        session_destroy();
        session_start();
        session_regenerate_id();
    }
}
