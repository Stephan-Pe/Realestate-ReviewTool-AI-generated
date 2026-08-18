<?php

/**
 * index.php — Application entry point
 *
 * Bootstraps autoloading, Twig, and the Router.
 * All requests are routed through this file.
 *
 * PHP version 8.0+
 */

// ============================================================
//  Composer autoloader (includes Twig)
// ============================================================
require __DIR__ . '/vendor/autoload.php';

// ============================================================
//  Custom autoloader (App\ and Core\ namespaces)
// ============================================================
spl_autoload_register(function (string $class): void {
    // App namespace
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/App/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
        return;
    }

    // Core namespace
    $prefix = 'Core\\';
    $baseDir = __DIR__ . '/Core/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

// ============================================================
//  Session
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
//  Request URI
// ============================================================
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$baseUrl    = '/';

// Strip base URL
if (strpos($requestUri, $baseUrl) === 0) {
    $requestUri = substr($requestUri, strlen($baseUrl));
}
$requestUri = rtrim($requestUri, '/') ?: '/';

// ============================================================
//  Routes
// ============================================================
$router = new \Core\Router();

// Home / valuation calculator
$router->add('/',                    ['controller' => 'Homes', 'action' => 'index']);

// AJAX endpoints
$router->add('homes/search',         ['controller' => 'Homes', 'action' => 'search']);
$router->add('homes/calculate',      ['controller' => 'Homes', 'action' => 'calculate']);
$router->add('homes/save',           ['controller' => 'Homes', 'action' => 'save']);

// Cookie Consent
$router->add('cookie/consent',       ['controller' => 'CookieConsent', 'action' => 'consent']);
$router->add('cookie/reject',        ['controller' => 'CookieConsent', 'action' => 'reject']);

// Valuation list
$router->add('homes/list',           ['controller' => 'Homes', 'action' => 'list']);

// Single valuation — GET
$router->add('homes/show/{id:\d+}',  ['controller' => 'Homes', 'action' => 'show']);

// Edit valuation form — GET
$router->add('homes/edit/{id:\d+}',  ['controller' => 'Homes', 'action' => 'edit']);

// Update valuation — PUT (AJAX)
$router->add('homes/update/{id:\d+}', ['controller' => 'Homes', 'action' => 'update']);

// Delete valuation — DELETE (AJAX)
$router->add('homes/delete/{id:\d+}', ['controller' => 'Homes', 'action' => 'delete']);

// ============================================================
//  Dispatch
// ============================================================
try {
    $router->dispatch($requestUri);
} catch (\Exception $e) {
    $code = $e->getCode() ?: 500;
    http_response_code($code);

    if ($code === 404) {
        echo '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><title>404</title></head><body>';
        echo '<h1>404 — Seite nicht gefunden</h1>';
        echo '<p>Die angeforderte Seite existiert nicht.</p>';
        echo '<a href="/">Zurück zur Startseite</a>';
        echo '</body></html>';
    } else {
        echo '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><title>Fehler</title></head><body>';
        echo '<h1>Fehler</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</body></html>';
    }
}
