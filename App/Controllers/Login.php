<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use App\Security\UserData;
use \App\Auth;
use \App\Flash;
use PragmaRX\Google2FA\Google2FA;
use \App\Security\TwoFactorAuth;
use \App\Services\TwoFactorService;
use \App\Security\Encryption;
use \App\Config;

/**
 * Login Controller
 * 
 * 
 * PHP version 8.0.22
 */
class Login extends \Core\Controller
{
    /**
     * Before filter to check CSRF token for POST <requests></requests>
     * 
     */
    protected function before() {}
    /**
     * Show the login page
     * 
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * Log in a user
     * 
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['user_email'], $_POST['user_password']);

        $is_robot = !empty($_POST['send_info']); // checkbox just for robots-scripts
        $remember_me = isset($_POST['remember_me']);

        if (!$user || $is_robot) {
            Flash::addMessage('Login fehlgeschlagen. Bitte prüfen Sie Ihre Daten.', FLASH::WARNING);
            $this->redirect('/login');
            return;
        }
        if (isset($user->id)) {
            $_SESSION['2fa_user_id'] = (int) $user->id;
            $_SESSION['2fa_remember_me'] = (bool) $remember_me;
        //$is_privileged = in_array($user->user_role, ['admin', 'super']);
        }
      

        if ($user) {
            // USER HAS 2FA: Send to verification code entry
            $this->redirect('/login/twofa');
        }
        return;
    }
    /**
     * Show the 2FA setup page with QR code
     * 
     * @return void
     */

    public function twofaAction()
    {
        // 1. Initialize the engines
        $google2fa = new Google2FA();
        $twoFactorAuth = new TwoFactorAuth($google2fa);
        $twoFactorService = new TwoFactorService($google2fa, $twoFactorAuth);
        $user = User::findById($_SESSION['2fa_user_id'] ?? null);

        if (!isset($_SESSION['temp_tfa_secret']) && empty($user->otp_secret)) {
            $_SESSION['temp_tfa_secret'] = $twoFactorAuth->generateSecret();
        }


        $secret = $_SESSION['temp_tfa_secret'] ?? $user->otp_secret ?? null;
        if (!$user) {
            $this->redirect('/login');
            return;
        }
        $userData = UserData::fromArray(['user_email' => $user->user_email]);
        $qrCodeUri = $twoFactorService->createQRCode($userData, $secret);

        View::renderTemplate('Login/twofa.html', [
            'qr_code' => $qrCodeUri,
            'secret'  => $secret,
            'user_name' => $user->user_name,
            'user_is_2fa' => $user->is_2fa_activated
        ]);
    }

    /**
     * Verify the 2FA code entered by the user
     * 
     * @return void
     */

    public function verifyAction(): void
    {
        if (!isset($_SESSION['2fa_user_id'])) {
            $this->redirect('/login');
            return;
        }

        $remember_me = $_SESSION['2fa_remember_me'] ?? false;
        $user_id = $_SESSION['2fa_user_id'] ?? null;
        $user = User::findById($user_id);
        // Fix the typo here: otp_secret
        if (empty($user->otp_secret)) {
            $secret = $_SESSION['temp_tfa_secret'] ?? null;
        } else {
            $secret = $user->otp_secret;
        }


        $code = $_POST['two_fa_code'] ?? null;
        // DEBUG BLOCK
        // echo "Source: " . $source . "<br>";
        // echo "Secret: " . $secret . "<br>";
        // echo "Code: " . $code . "<br>";
        // echo "Timestamp: " . time() . "<br>";

        $google2fa = new Google2FA();
        $twoFactorAuth = new TwoFactorAuth($google2fa);
        $twoFactorService = new TwoFactorService($google2fa, $twoFactorAuth);

        $is_valid = $twoFactorService->verifyCode($secret, $code, 2);
        //$is_privileged = in_array($user->user_role, ['admin', 'super']);
        // var_dump($is_valid);
        // exit;

        if ($is_valid) {
            // Update the user's 2FA status in the database

            if (isset($_SESSION['temp_tfa_secret'])) {

                $user->activateTwoFa($secret);
                Flash::addMessage('Two-Factor Authentication erfolgreich aktiviert.');
                unset($_SESSION['temp_tfa_secret']);
            } elseif ($user->is_2fa_activated) {
                Flash::addMessage('Two-Factor Authentication erfolgreich verifiziert.');
            }


            unset($_SESSION['2fa_user_id']);
            Auth::login($user, $remember_me ?? false);

            $this->redirect(Auth::getReturnToPage());
        } else {
            Flash::addMessage('Ungültiger 2FA-Code.', FLASH::WARNING);
            $this->redirect('/login');
        }
    }

    /**
     * Log out a user
     * 
     * 
     * @return void
     */
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a logged out flash message and redirect to the homepage.
     * Because $_SESSION is destroyed a new action needs to be called
     * in order to use the $_SESSION
     * 
     * @return void
     */
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Logout erfolgreich');

        $this->redirect('/');
    }
}
