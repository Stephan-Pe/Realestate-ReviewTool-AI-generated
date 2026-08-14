<?php

namespace App\Middleware;

class CaptchaMiddleware
{
    /**
     * Handle the incoming request and validate the CAPTCHA for POST requests.
     * If the CAPTCHA is invalid, a 403 response is returned and the request is not processed further.
     * If the CAPTCHA is valid or it's not a POST request, the request is passed to the next middleware or controller.
     * Logs invalid CAPTCHA checks to a file for monitoring purposes.
     */
    public function handle($request, $next)
    {
        // Check if the request method is POST and if the captcha value is set
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['captcha_value'])) {
            // Validate the CAPTCHA
            if (!static::checkCaptcha()) {
                // If the CAPTCHA is invalid, add a flash message and redirect back to the form
                http_response_code(403);
                return false; // ← IMPORTANT
            }
        }

        // If the CAPTCHA is valid or it's not a POST request, continue to the next middleware or controller
        return $next($request);
    }

    /**
     * Validates the CAPTCHA value from the POST request against the expected value stored in the session.
     * Returns true if the CAPTCHA is valid, false otherwise.
     */
    public static function checkCaptcha(): bool
    {
        $captchaValue = trim($_POST['captcha_value'] ?? '');
        $expectedValue = $_SESSION['captcha'] ?? '';

        return hash_equals($expectedValue, $captchaValue);
    }
}