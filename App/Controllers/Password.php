<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Password controller
 * 
 * PHP version 8.2.12
 * 
 */
class Password extends \Core\Controller
{
    /**
     * Show the forgotten password page
     * 
     * @return void
     */
    public function forgotAction()
    {
        View::renderTemplate('Password/forgot.html');
    }

    /**
     * Send the password reset link to the supplied email
     * 
     * @return void
     */
    public function requestResetAction()
    {
        User::sendPasswordReset($_POST['email']);
        View::renderTemplate('Password/reset_requested.html');
    }

    /**
     * Show the reset password form
     * 
     * @return void
     */
    public function resetAction()
    {
        $token = $this->route_params['token'];

        $user = $this->getUserOrExit($token);

            View::renderTemplate('Password/reset.html', [
                'token' => $token
            ]);
    }

    /**
     * Reset the user's password
     * 
     * 
     * @return void
     */
    public function resetPasswordAction()
    {
        // 1. Clear all session data
        $_SESSION = [];

        // 2. Destroy the session cookie
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
        // 3. Destroy the session on the server
        session_destroy();
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);

        if ($user->resetPassword($_POST['password'])) {
            View::renderTemplate('Password/reset_success.html');
        } else {
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }

    /**
     * Find the user model associated with the password reset token, or end the request with a message
     * 
     * @param string $token Password reset token send to user
     * 
     * @return mixed User object if found and the token has'nt expired, null otherwise
     */
    protected function getUserOrExit(string $token)
    {
        $user = User::findByPasswordReset($token);

        
        if ($user) {

           return $user;
        } else {
            View::renderTemplate('Password/token_expired.html');
            exit;
        }
    }
}
