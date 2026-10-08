<?php

namespace App\Middleware;

class CsrfMiddleware
{
    protected const BASE_PATH = __DIR__ . '/../..';

    public function handle($request, $next)
    {
        $logDir = self::BASE_PATH . DIRECTORY_SEPARATOR . 'logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . DIRECTORY_SEPARATOR . 'error.log';
        // error_log('===== CSRF CHECK =====');

        // error_log(
        //     'Session ID: ' . session_id()
        // , 3, $logFile);

        // error_log(
        //     'Session CSRF: ' .
        //         ($_SESSION['csrf_token'] ?? 'NOT SET')
        // , 3, $logFile);

        // error_log(
        //     'POST CSRF: ' .
        //         ($_POST['csrf_token'] ?? 'NOT SET')
        // , 3, $logFile);

        // error_log(
        //     'Header CSRF: ' .
        //         ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? 'NOT SET')
        // , 3, $logFile);

        // error_log(
        //     'Content-Type: ' .
        //         ($_SERVER['CONTENT_TYPE'] ?? 'NOT SET')
        // , 3, $logFile);

        // error_log(
        //     'Accept: ' .
        //         ($_SERVER['HTTP_ACCEPT'] ?? 'NOT SET')
        // , 3, $logFile);
        // Read raw input for JSON requests
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $uri = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
        $sessionId = session_id();
        $sessionToken = $_SESSION['csrf_token'] ?? '';


    if ($method === 'POST') {

    $postedToken = '';

    # 1. Normaler HTML-Formular-POST
    if (isset($_POST['csrf_token'])) {
        $postedToken = $_POST['csrf_token'];
    }

    # 2. Header-Check nach oben ziehen oder abfragen (viele JS-Fetch-Libs nutzen X-CSRF-Token)
    if (empty($postedToken) && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $postedToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }

    # 3. AJAX / JSON request
    if (
        empty($postedToken) &&
        isset($_SERVER['CONTENT_TYPE']) &&
        str_contains($_SERVER['CONTENT_TYPE'], 'application/json')
    ) {
        # Falls php://input schon an anderer Stelle gelesen wurde oder noch gelesen werden muss:
        $rawBody = file_get_contents('php://input');
        $rawInput = json_decode($rawBody, true);

        if (is_array($rawInput)) {
            $postedToken = $rawInput['csrf_token'] ?? '';

            # Speichern für spätere Nutzung im Controller
            $_SERVER['JSON_INPUT'] = $rawInput;
            $request['json'] = $rawInput; # Falls du ein Request-Objekt weitergibst
        }
    }

    # 4. Token validieren
    if (
        empty($sessionToken) ||
        empty($postedToken) ||
        !hash_equals($sessionToken, $postedToken)
    ) {
        $logMessage = sprintf(
            "[%s] CSRF Mismatch: Session '%s' vs Posted '%s'\n",
            date('Y-m-d H:i:s'),
            $sessionToken,
            $postedToken
        );

        error_log($logMessage, 3, $logFile);

        $isJsonRequest = (
            (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
            (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        );

        if ($isJsonRequest) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');

            echo json_encode([
                'success' => false,
                'error'   => 'CSRF Token Mismatch'
            ]);

            exit;
        }

        http_response_code(403);
        echo '403 Forbidden: Invalid CSRF Token';
        exit;
    }
}

// Weiterleitung zum nächsten Handler
return $next($request);
    }
}
