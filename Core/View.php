<?php

namespace Core;

use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\String\StringExtension;
use Core\AssetExtension;
use App\Auth;


/**
 * View
 * 
 * PHP version 8.0.22
 */

class View
{

    /**
     * Render a view .phtml file
     *
     * @param string $view  The view file
     *
     * @return void
     */
    public static function renderPhtml($view, $args)
    {
        $file = "../App/Views/$view";  // relative to Core directory

        if (!file_exists($file)) {
            return '';
        }
        if (is_array($args)) {
            extract($args);
        }
        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {

        echo static::getTemplate($template, $args);
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return string
     */
    public static function getTemplate($template, $args = [])
    {
        static $twig = null;

        $env = $_SERVER['APP_ENV'] ?? 'prod';
        $dev = $env === 'dev';

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            // $twig = new \Twig\Environment($loader);
            $twig = new \Twig\Environment($loader);
            $twig->addExtension(new IntlExtension());
            $twig->addExtension(new StringExtension());
            $twig->addExtension(new AssetExtension(dirname(__DIR__) . '/public/rev-manifest.json', $dev));
            $twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Zurich');
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            $twig->addFunction(new \Twig\TwigFunction('hasRole', function ($role) {
                $user = Auth::getUser();
                $levels = ['guest' => 0, 'user' => 1, 'manager' => 2, 'admin' => 3, 'super' => 4];
                return $user && $levels[$user->user_role] >= $levels[$role];
            }));
            $twig->addFunction(new \Twig\TwigFunction('csrf_field', function () {
                return \App\Services\CsrfService::csrf_field();
            }, ['is_safe' => ['html']]));

            if ($dev) {
                $twig->enableDebug();
            }
        }
        //return $twig->render($template, $args);
        $content = $twig->render($template, $args);

        // Calculate the Content-Length
        $contentLength = strlen($content);
        // Set the Content-Length header
        if (!headers_sent()) {
            if (Auth::getUser()) {

                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                header('Pragma: no-cache');
                header('Expires: 0');
                header("Content-Length: " . $contentLength);
                header("Service-Worker-Allowed: /sw.js");
                header("X-Content-Type-Options: nosniff");
                header("X-Frame-Options: DENY");
                header("X-XSS-Protection: 1; mode=block");
                header("Referrer-Policy: strict-origin-when-cross-origin");
                header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
            } else {

                header("Cache-Control: max-age=604800, must-revalidate");
                header("Pragma: cache");
                header("Content-Length: " . $contentLength);
                header("Service-Worker-Allowed: /sw.js");
                header("X-Content-Type-Options: nosniff");
                header("X-Frame-Options: DENY");
                header("X-XSS-Protection: 1; mode=block");
                header("Referrer-Policy: strict-origin-when-cross-origin");
                header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
            }
        }
        return $content;
    }
}
