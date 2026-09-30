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
        error_log('===== CSRF CHECK =====');

        error_log(
            'Session ID: ' . session_id()
        , 3, $logFile);

        error_log(
            'Session CSRF: ' .
                ($_SESSION['csrf_token'] ?? 'NOT SET')
        , 3, $logFile);

        error_log(
            'POST CSRF: ' .
                ($_POST['csrf_token'] ?? 'NOT SET')
        , 3, $logFile);

        error_log(
            'Header CSRF: ' .
                ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? 'NOT SET')
        , 3, $logFile);

        error_log(
            'Content-Type: ' .
                ($_SERVER['CONTENT_TYPE'] ?? 'NOT SET')
        , 3, $logFile);

        error_log(
            'Accept: ' .
                ($_SERVER['HTTP_ACCEPT'] ?? 'NOT SET')
        , 3, $logFile);
        // Read raw input for JSON requests
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $uri = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
        $sessionId = session_id();
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        // Ensure session token exists
        if (empty($sessionToken)) {
            $sessionToken = \App\Services\CsrfService::getToken();
        }

        error_log(
            sprintf(
                "[%s] CSRF middleware reached: %s %s | session_id=%s | csrf=%s\n",
                date('Y-m-d H:i:s'),
                $method,
                $uri,
                $sessionId,
                $sessionToken
            ),
            3,
            $logFile
        );

        if ($method === 'POST') {

            $postedToken = '';

            // 1. Normaler HTML-Formular-POST
            if (isset($_POST['csrf_token'])) {
                $postedToken = $_POST['csrf_token'];
            }

            // 2. AJAX / JSON request
            if (
                empty($postedToken) &&
                isset($_SERVER['CONTENT_TYPE']) &&
                str_contains($_SERVER['CONTENT_TYPE'], 'application/json')
            ) {
                $rawInput = json_decode(file_get_contents('php://input'), true);

                if (is_array($rawInput)) {
                    $postedToken = $rawInput['csrf_token'] ?? '';

                    // JSON-Daten für den Controller speichern
                    $_SERVER['JSON_INPUT'] = $rawInput;
                }
            }

            // 3. Optional: CSRF Token aus Header
            if (
                empty($postedToken) &&
                isset($_SERVER['HTTP_X_CSRF_TOKEN'])
            ) {
                $postedToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
            }

            // 4. Token validieren
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

                $isJsonRequest =
                    (
                        isset($_SERVER['HTTP_ACCEPT']) &&
                        str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
                    )
                    ||
                    (
                        isset($_SERVER['CONTENT_TYPE']) &&
                        str_contains($_SERVER['CONTENT_TYPE'], 'application/json')
                    );

                if ($isJsonRequest) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    header('X-Content-Type-Options: nosniff');

                    echo json_encode([
                        'error' => 'CSRF Token Mismatch'
                    ]);

                    exit;
                }

                http_response_code(403);
                echo '403 Forbidden: Invalid CSRF Token';
                exit;
            }
        }

        // Continue to next step
        return $next($request);
    }
}
