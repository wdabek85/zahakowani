<?php

namespace Ilabs\Inpost_Pay\Lib;

class BrowserIdStorage
{
    public static string $browserId = '';

    public static function get(): string
    {
        if (isset($_COOKIE['BrowserId'])) {
            self::set($_COOKIE['BrowserId']);
        }
        return self::$browserId;
    }

    /**
     * @param string $browserId
     */
    public static function set(string $browserId): void
    {
        self::$browserId = $browserId;
    }

    public static function drop(): void
    {
        self::$browserId = '';
    }

    public static function isSet(): bool
    {
        return self::$browserId !== '';
    }

    public static function compare(string $browserId): bool
    {
        return self::$browserId === $browserId;
    }
}