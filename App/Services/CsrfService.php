<?php

namespace App\Services;

use \App\Security\TokenGenerator;

class CsrfService
{
    /**
     * Get or generate a CSRF token for the current session
     * @return string The CSRF token
     * Usage in Helper.php Helper::csrf_field() to create a hidden input field with the token for form protection
     */
public static function getToken(): string
{
    // if (session_status() === PHP_SESSION_NONE) {
    //     session_start();
    // }

    // error_log(
    //     'CSRF GET TOKEN: session_id=' . session_id() .
    //     ' csrf_before=' . ($_SESSION['csrf_token'] ?? 'EMPTY')
    // );

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = TokenGenerator::generateRandomString();
    }

    // error_log(
    //     'CSRF GET TOKEN: session_id=' . session_id() .
    //     ' csrf_after=' . $_SESSION['csrf_token']
    // );

    return $_SESSION['csrf_token'];
}

        /**
     * Create a hidden input field with the CSRF token for form protection
     * @return string HTML input field with the CSRF token
     * Usage: in View.php     $twig->addFunction(new \Twig\TwigFunction('csrf_field', function () { return \Core\Helper::csrf_field();}, ['is_safe' => ['html']]));
     * in Twig template: {{ csrf_field()|raw }}
     */
    public static function csrf_field(){
        $csrf_token = static::getToken();
        return '<input type="hidden" name="csrf_token" value="' . $csrf_token . '">';
    }
}
