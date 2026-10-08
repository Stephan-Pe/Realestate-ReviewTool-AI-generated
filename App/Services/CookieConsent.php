<?php

namespace App\Services;

class CookieConsent
{
    private static array $default = [
        'analytics' => false,
        'marketing' => false,
        'necessary' => true
    ];

    /**
     * Retrieve the current consent choices from the request cookie header
     */
    public static function getConsent(): array
    {
        if (!self::hasConsent()) {
            return self::$default;
        }

        $decoded = json_decode($_COOKIE['cookie_consent'], true);

        if (!is_array($decoded)) {
            return self::$default;
        }

        return array_merge(self::$default, $decoded);
    }

    /**
     * Check if the user has already made a consent decision
     */
    public static function hasConsent(): bool
    {
        return isset($_COOKIE['cookie_consent']) && !empty($_COOKIE['cookie_consent']);
    }

    /**
     * Helper for template rendering decisions
     */
    public static function isPending(): bool
    {
        return !self::hasConsent();
    }
}