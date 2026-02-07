<?php

class CookieManager
{
    // Crear cookie
    public static function set(
        string $name,
        string $value,
        int $seconds = 86400 // 1 día
    ): void {
        setcookie(
            $name,
            $value,
            time() + $seconds,
            '/',       // path
            '',        // domain
            false,     // secure (true si HTTPS)
            true       // httponly
        );
    }

    // Leer cookie
    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    // Borrar cookie
    public static function delete(string $name): void
    {
        setcookie($name, '', time() - 3600, '/');
    }
}