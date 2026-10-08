<?php

namespace App\Controllers;

use \Core\Helper;

class Cookies extends \Core\Controller
{
    private int $cookieLifetime = 31536000; // 1 year in seconds

    /**
     * Accept cookie consent via AJAX
     */
    public function consentAction(): void
    {
        header('Content-Type: application/json');
        // Step 1: Extract JSON input sent via fetch body
        $input = Helper::getJsonInput();

        if (!is_array($input)) {
            $input = [];
        }

        // Step 2: Enforce CSRF protection on ALL consent modifications
        if (!$this->validateCsrf($input)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error'   => 'Ungültiger CSRF-Token'
            ]);
            return;
        }

        // Step 3: Sanitize and build the consent payload
        $cookieConsent = [
            'analytics' => (bool)($input['analytics'] ?? false),
            'marketing' => (bool)($input['marketing'] ?? false),
            'necessary' => true
        ];

        // Step 4: Issue the HTTP response header to store the cookie
        $this->setConsentCookie($cookieConsent);

        echo json_encode([
            'success' => true,
            'message' => 'Cookie preferences saved',
            'consent' => $cookieConsent
        ]);
    }
    /**
     * Validate CSRF token from request
     * @return bool
     */
    private function validateCsrf(array $input): bool
    {
        $clientToken = $input['csrf_token'] ?? '';
        $serverToken = $_SESSION['csrf_token'] ?? '';

        if (empty($clientToken) || empty($serverToken)) {
            return false;
        }

        return hash_equals($serverToken, $clientToken);
    }
    /**
     * Set the cookie_consent cookie header
     */
    private function setConsentCookie(array $consent): void
    {
        $expires = time() + $this->cookieLifetime;

        setcookie(
            'cookie_consent',
            json_encode($consent),
            [
                'expires'  => $expires,
                'path'     => '/',
                'secure'   => true,      // Requires HTTPS
                'httponly' => false,     // Allow JS to read consent state if needed
                'samesite' => 'Lax'
            ]
        );
    }

    // /**
    //  * Reject all non-essential cookies (reset to defaults)
    //  */
    // public function rejectAction(): void
    // {
    //     // Use JSON_INPUT set by CsrfMiddleware (php://input can only be read once)
    //     $input = Helper::getJsonInput();

    //     if (!is_array($input)) {
    //         $input = [];
    //     }


    //     $cookieConsent = [
    //         'analytics' => false,
    //         'marketing' => false,
    //         'necessary' => true
    //     ];

    //     $this->setConsentCookie($cookieConsent);

    //     header('Content-Type: application/json');

    //     echo json_encode([
    //         'success' => true,
    //         'message' => 'Non-essential cookies rejected'
    //     ]);
    // }
}