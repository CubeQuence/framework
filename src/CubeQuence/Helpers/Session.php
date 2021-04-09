<?php

declare(strict_types=1);

namespace CQ\Helpers;

// use CQ\Crypto\Symmetric;

final class Session
{
    /**
     * Set session var.
     */
    public static function set(string $name, $data) : mixed
    {
        // TODO: encrypt
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
     */
    public static function get(string $name) : mixed
    {
        return $_SESSION[$name] ?? null;
    }

    /**
     * Destroy and restart session.
     */
    public static function destroy(): void
    {
        session_destroy();
        session_start();
        session_regenerate_id();
    }
}
