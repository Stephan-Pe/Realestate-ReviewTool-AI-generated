<?php 

namespace App\Controllers;

/**
 * Authenticate base Controller
 * 
 * PHP version 8.0.22
 */
abstract class Authenticated extends \Core\Controller
{
   
    /**
     * Use the before method to authenticate the user for all Class methods.
     * Otherwise call requireLogin in nessesary methods
     * 
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();
    }
}