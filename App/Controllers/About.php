<?php

namespace App\Controllers;

use \Core\View;

/**
 * About Controller the impressum page
 * 
 * PHP version 8.2.12
 * 
 */

class About extends \Core\Controller
{
    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        // echo "(before) ";

    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        // echo " (after)";
    }

    /**
     * Show index page
     *
     *@return void
     */
    public function indexAction()
    {
        // echo 'Hello from the index action in the Home controller';

        View::renderTemplate('About/index.html');
    }
}
