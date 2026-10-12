## Twig View Engine

```php
<?php

namespace Core;

use Twig\Extra\Intl\IntlExtension;
use App\Auth;

/**
 * View
 * 
 * PHP version 8.2.12
 */

class View
{

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
     * @param mixed $args  Associative array of data to display in the view (optional)
     *
     * @return string
     */
    public static function getTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addExtension(new IntlExtension());
            $twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Zurich');
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
        }

          //return $twig->render($template, $args);
        $content = $twig->render($template, $args);

               // Calculate the Content-Length
        $contentLength = strlen($content);
        // Set the Content-Length header
        if (!headers_sent()) {
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
        return $content;
    }
}
```