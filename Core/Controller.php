<?php

namespace Core;

use \App\Auth;
use \App\Flash;
use Doctrine\ORM\EntityManager;

/**
 * Base controller
 *
 * PHP version 8.0.22
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected array $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct(array $route_params)
    {
        $this->route_params = $route_params;
    }
    /**
     * Mimic Symfony's getDoctrine() shortcut safely.
     * 
     * @return EntityManager
     */
    protected function getDoctrine(): EntityManager
    {
        // This assumes you have a setup file or container that initializes Doctrine.
        // Replace \App\Database::getEntityManager() with wherever your EntityManager is instantiated.
        return \App\Database::getEntityManager();
    }
    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {

        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            // echo "Method $method not found in controller " . get_class($this);
            throw new \Exception("Method $method not found in controller " .
                get_class($this));
        }
    }


    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before() {}

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after() {}

    /**
     * Redirect to different page
     *
     * @param string $url The relative URL
     */
    protected function redirect($url)
    {

        header('Location: https://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    /**
     * On restricted pages require the user to log in first.
     * Remeber the requested page to redirect him there
     * 
     * @return void
     */
    public function requireLogin()
    {
        if (!Auth::getUser()) {
            Flash::addMessage('Please login to access that page', Flash::INFO);
            Auth::rememberRequestPage();

            $this->redirect('/login');
        }
    }
}
