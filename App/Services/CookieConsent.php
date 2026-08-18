<?php

namespace App\Services;

class CookieConsent
{
    private array $default = [
        'analytics' => false,
        'marketing' => false,
        'necessary' => true
    ];

    /**
     * Get the current cookie consent state
     * Returns default if no consent cookie exists
     */
    public static function getConsent(): array
    {
        if (!self::hasConsent()) {
            return (new self())->default;
        }

        $decoded = json_decode($_COOKIE['cookie_consent'], true);

        if (!is_array($decoded)) {
            return (new self())->default;
        }

        return array_merge((new self())->default, $decoded);
    }

    /**
     * Check if user has given any consent (cookie exists)
     */
    public static function hasConsent(): bool
    {
        return isset($_COOKIE['cookie_consent']) && !empty($_COOKIE['cookie_consent']);
    }

    /**
     * Check if analytics cookies are allowed
     */
    public function hasAnalyticsConsent(): bool
    {
        $consent = $this->getConsent();
        return (bool)($consent['analytics'] ?? false);
    }

    /**
     * Check if marketing cookies are allowed
     */
    public function hasMarketingConsent(): bool
    {
        $consent = $this->getConsent();
        return (bool)($consent['marketing'] ?? false);
    }

    /**
     * Check if user has not yet given consent (first visit)
     */
    public function isPending(): bool
    {
        return !$this->hasConsent();
    }
}