<?php

namespace App\Controllers;

use \Core\Helper;
use \Core\View;
use \App\Models\User;
use \App\Flash;

/**
 * SignUp Controller
 * 
 * PHP 7.4
 */

class Signup extends \Core\Controller
{
    public ?string $csrf_token = null; 
    public ?string $image = null;
    public ?string $captcha = null;
    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
  
    }

   
    /**
     *Show signup page 
     *
     *@return void
     */
    public function newAction()
    {

        View::renderTemplate('Signup/new.html', [
            'old' => $_SESSION['old_input'] ?? []
        ]);
    }

    /**
     * Summary of captchaAction
     * @return never
     */
    public function captchaAction()
    {
        Helper::outputCaptchaImage();
        exit;
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
         Flash::addMessage('Funktion nicht aktiviert', FLASH::SUCCESS);
        $this->redirect('/signup');
        return;
        //       // Captcha & Rate Limiting 
        // if (!$this->checkCaptcha()) {
        //     $f = sys_get_temp_dir() . '/rl_' . md5($_SERVER['REMOTE_ADDR']);
        //     $count = is_file($f) ? (int) file_get_contents($f) : 0;

        //     if ($count > 5 && filemtime($f) > time() - 60) {
        //         $wait = 60 - (time() - filemtime($f));
        //         Flash::addMessage("Zu viele Versuche — warten Sie {$wait} Sekunden", Flash::WARNING);
        //         $this->redirect('/signup');
        //         return;
        //     }

        //     file_put_contents($f, $count + 1, LOCK_EX);
        //     Flash::addMessage('Captcha inkorrekt 🔥', Flash::WARNING);
        //     $this->redirect('/signup');
        //     return;
        // }

        // // Success
        // $f = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_' . md5($_SERVER['REMOTE_ADDR']);
        // if (is_file($f)) unlink($f);
        //   // User Data

        // $user = new User($_POST);
     

        // if ($user->save()) {
          
        //     $user->sendActivationEmail();
        //     $this->redirect('/signup/success');
        // } else {
        //     $_SESSION['old_input'] = $_POST;
        //     View::renderTemplate('Signup/new.html', [
        //         'old' => $_SESSION['old_input'] ?? []
        //     ]);
        // }
    }

    /* Check if the captcha value is correct
     * 
     * @return bool
     */

    public function checkCaptcha()
    {
        if (!isset($_POST['captcha_value'], $_SESSION['captcha'])) {
            return false;
        }
        $captchaValue = $_SESSION['captcha'];
        $userCaptcha  = trim($_POST['captcha_value']);

        return $captchaValue === $userCaptcha;
    }

    /**
     * Show the signup success page
     * 
     * @return void
     */

    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }

    /**
     * Activate new account
     * 
     * @return void
     */

 public function activateAction()
{
    // Find the user by the token hash
    $user = User::findByActivationToken($this->route_params['token']);
  
    if ($user) {
        $user->activate();
        $this->redirect('/signup/activated');
    } else {
        // Token was invalid or expired
        Flash::addMessage('Ungültiger oder abgelaufener Aktivierungslink.', Flash::WARNING);
        $this->redirect('/signup');
    }
}

    /**
     * Show the activation success page
     * 
     * @return void:
     */
    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}
https://onepager.test/signup/activate/5b832d64a1b9dd2505f353a1588b66018861601e0b40488e1cd4ca5e3f506f32
