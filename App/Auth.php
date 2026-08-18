<?php

namespace App;

use \App\Models\RememberedLogin;
use \App\Models\User;

/**
 * Authentication
 * 
 * 
 * PHP version 8.2.12
 */

class Auth
{
    protected static mixed $user;// Static cache for the current logged in user model
    /**
     * Log in a user by starting a session and optionally setting a remember-me cookie.
     *
     * @param User $user
     * @param bool $rememberMe
     * @return void
     */
    public static function login(User $user, bool $rememberMe = false): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

        if ($rememberMe && $user->rememberLogin()) {
            setcookie('remember_me', (string) $user->remember_token, [
                'expires'  => (int) $user->expiry_timestamp,
                'path'     => '/',
                'domain'   => '',
                'secure'   => true,       // HTTPS only
                'httponly' => true,       // Prevents XSS script access
                'samesite' => 'Lax',      // Protects against CSRF
            ]);
        }
    }


    /**
     * Logout the user
     * 
     * @return void
     */
    public static function logout()
    {
        // Unset all of the session variables.
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        // Start a fresh, clean session for a new login if user wants to login immediatly
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            session_regenerate_id(true);
        }

        $_SESSION['csrf_token'] = \App\Security\TokenGenerator::generateRandomString();

        static::forgetLogin();
    }

    /**
     * Remeber requested restriced page and redirect in the session
     * 
     * @return void
     */
    public static function rememberRequestPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally requested page to return to after requirng login || redirect to home
     * 
     * @return string
     */
    public static function getReturnToPage(): string
    {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current logged in user, from the session or remember_me cookie
     * 
     * @return mixed The user model if logged in, null otherwise
     */

    public static function getUser()
    {
        // If we already fetched the user in this request, return the cached version
        if (isset(static::$user)) {
            return static::$user;
        }

        if (isset($_SESSION['user_id'])) {
            static::$user = User::findByID($_SESSION['user_id']);
        } else {
            static::$user = static::loginFromRememberCookie();
        }

        return static::$user;
    }

    /**
     * Login the user from remember_me cookie
     * 
     * @return mixed The user model if login cookie found, null otherwise
     */
    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if ($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);

            if ($remembered_login && !$remembered_login->hasExpired()) {
                $user = $remembered_login->getUser();

                static::login($user, false);

                return $user;
            }
        }
    }

    /**
     * Forget the remebered login, if present
     * 
     * @return void
     */
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        $remembered_login = RememberedLogin::findByToken($cookie);

        if ($remembered_login) {
            $remembered_login->delete();
        }
        setcookie('remember_me', '', time() - 3600); // set expire to value in the past
    }
}
