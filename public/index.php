<?php

/**
 * Front Controller
 * 
 * PHP version 8.2.12
 */
// echo 'REQUESTED URL = "' . $_SERVER['QUERY_STRING'] . '"';

// ini_set('session.cookie_lifetime', '864000'); // ten days in seconds

/**
 * Twig
 */
// load .env BEFORE anything else
$envFile = file_exists(__DIR__ . '/../.env') ? parse_ini_file(__DIR__ . '/../.env') : [];
$_SERVER['APP_ENV'] = $envFile['APP_ENV'] ?? 'prod';

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Autoloader
 */
// spl_autoload_register(function ($class) {
//     $root = dirname(__DIR__); // get parent directory
//     $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
//     if(is_readable($file)) {
//         require $root . '/' . str_replace('\\', '/', $class) . '.php';
//     }
// });
/**
 * Error and Exceptionhandling
 * 
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 * 
 */

function sec_session_start()
{
    $session_name = 'sec_session_id';

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $httponly = true;

    if (ini_set('session.use_only_cookies', 1) === false) {
        header("Location: ../error.php?err=Session init failed");
        exit();
    }

    session_name($session_name);

    session_set_cookie_params([
        'lifetime' => 0,          // until browser closes
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => $httponly,
        'samesite' => 'Strict',   // or 'Lax'
    ]);

    session_start();
}


sec_session_start();

// Check if the user agent is set in the session, if not set it to the current user agent
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}


// Load .env if exists (for local switching)
$envFile = file_exists(__DIR__ . '/../.env')
    ? parse_ini_file(__DIR__ . '/../.env')
    : [];

// Determine environment
$appEnv = $_SERVER['APP_ENV']
    ?? $envFile['APP_ENV']
    ?? 'prod'; // fallback

define('APP_ENV', $appEnv);

// Create dev flag
$dev = APP_ENV === 'dev';


/**
 * Routing
 */

$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Homes', 'action' => 'index']);

// --- Real Estate Review (Bewertung) routes ---
$router->add('homes/search', ['controller' => 'Homes', 'action' => 'search']);
$router->add('homes/calculate', ['controller' => 'Homes', 'action' => 'calculate']);
$router->add('home/calculate', ['controller' => 'Homes', 'action' => 'calculate']);
$router->add('homes/save', ['controller' => 'Homes', 'action' => 'save']);
$router->add('homes/show/{id:\d+}', ['controller' => 'Homes', 'action' => 'showValuation']);
$router->add('homes/editValuation/{id:\d+}', ['controller' => 'Homes', 'action' => 'editValuation']);
$router->add('homes/deleteValuation/{id:\d+}', ['controller' => 'Homes', 'action' => 'deleteValuation']);
$router->add('homes/list', ['controller' => 'Homes', 'action' => 'list']);
$router->add('homes/captcha', ['controller' => 'Homes', 'action' => 'captcha']);

// --- Cookie Consent routes ---
$router->add('cookies/consent', ['controller' => 'Cookies', 'action' => 'consent']);
$router->add('cookies/reject', ['controller' => 'Cookies', 'action' => 'reject']);

// --- Original routes ---
$router->add('about', ['controller' => 'About', 'action' => 'index']);

// User Routes
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('settings', ['controller' => 'Settings', 'action' => 'new']);
$router->add('signup', ['controller' => 'Signup', 'action' => 'new']);
$router->add('signup/captcha', ['controller' => 'Signup', 'action' => 'captcha']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
// route password reset
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
// route activate signup process
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');

// Admin Route
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

// ===================================================================
//  CSRF Protection (POST only)
// ===================================================================

$csrf = new \App\Middleware\CsrfMiddleware();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $csrf->handle($_POST, fn() => true);

    if ($result === false) {
        App\Flash::addMessage('Starten Sie Ihren Browser neu und versuchen Sie es erneut.', App\Flash::WARNING);
        header('Location: ' . \App\Auth::getReturnToPage());
        exit();
    }
}

// ===================================================================
//  URL Validation — Whitelist allowed routes
// ===================================================================

/**
 * Allowed routes per HTTP method.
 * Only URLs in this list will be dispatched.
 * Everything else → 404 (no router execution, no risk).
 */
$allowedRoutes = [
    // GET routes
    'GET' => [
        '',
        'about',
        'login',
        'settings',
        'signup',
        'signup/captcha',
        'logout',
        'homes/captcha',
        'homes/list',
        'homes/search',
        'home/calculate',
    ],
    // POST routes
    'POST' => [
        'login/create',
        'signup/create',
        'homes/search',
        'homes/calculate',
        'home/calculate',
        'homes/save',
        'homes/captcha',
        'signup/captcha',
        'cookies/consent',
        'cookies/reject',
    ],
    // DELETE routes
    'DELETE' => [],  // handled by catch-all {controller}/{id}/{action}
];

// Normalize URL: strip leading slash, resolve empty to ''
$rawUrl = $_GET['url'] ?? $_SERVER['QUERY_STRING'] ?? '';
// $normalizedUrl = ltrim($rawUrl, '/');
// if ($normalizedUrl === '') {
//     $normalizedUrl = '';
// }

// Validate the URL against the allowed whitelist
$method = $_SERVER['REQUEST_METHOD'];
$allowed = $allowedRoutes[$method] ?? $allowedRoutes['GET'];

if (!in_array($rawUrl, $allowed)) {
    // URL not in whitelist → reject immediately, no router execution
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '404 — Not Found';
    exit;
}
// // Display the routing table
// echo '<pre>';
// var_dump($router->getRoutes());
// echo htmlspecialchars(print_r($router->getRoutes(), true));
// echo '</pre>';


// URL is whitelisted → safe to dispatch
$router->dispatch($rawUrl);
