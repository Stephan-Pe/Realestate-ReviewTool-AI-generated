<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\User;
use \App\Models\Home;
use \App\Middleware\AccessControl;

use \App\Flash;

/**
 * User admin controller
 *
 * PHP version 8.0.22
 */
class Users extends \Core\Controller
{

    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        // Make sure an admin user is logged in for example
        // return false;
        $this->requireLogin();
        AccessControl::requireRole('manager');
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {

        //echo 'User admin index';
        $galleries = Home::getsalesImagesForAdmin();

        $cards = Home::getAll();

        $users = User::findUsersForAdmin();

        $intruders = User::findIntrudersForAdmin();
        //echo 'User admin index';
        View::renderTemplate('Admin/index.html', [
            'galleries' => $galleries,
            'cards' => $cards,
            'users' => $users,
            'intruders' => $intruders
        ]);
    }
    /**
     * Soft delete of user, deactivate
     * 
     * @return void
     */
    public function deactivateAction()
    {
        $id = $_POST['user_id'];
        $deactivatedUser = new User($_POST);
        $deactivatedUser->deactivate($id);
        $this->redirect('/admin/users/index');
    }
}
