<?php

namespace App\Middleware;

use Twig\Node\Expression\Binary\AndBinary;

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
            // 1. Read input from $_POST or from JSON request body and parse JSON request body
            $postedToken =
                $rawInput['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (empty($postedToken)) {
                $rawInput = json_decode(file_get_contents('php://input'), true);
                if (is_array($rawInput)) {
                    $postedToken = $rawInput['csrf_token'] ?? '';
                    // Store decoded input for controller to reuse (php://input can only be read once)
                    $_SERVER['JSON_INPUT'] = $rawInput;
                }
            }  
            // 2. Validate the token
            if (empty($sessionToken) || !hash_equals($sessionToken, $postedToken)) {
                $logMessage = sprintf(
                    "[%s] CSRF Mismatch: Session '%s' vs Posted '%s'\n",
                    date('Y-m-d H:i:s'),
                    $sessionToken,
                    $postedToken
                );
                error_log($logMessage, 3, $logFile);

                // Check if request expects JSON (AJAX calls)
                $isJsonRequest = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
                    || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));

                if ($isJsonRequest) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    header('X-Content-Type-Options: nosniff');
                    echo json_encode(['error' => 'CSRF Token Mismatch']);
                    exit;
                }

                http_response_code(403);
                echo "403 Forbidden: Invalid CSRF Token";
                exit;
            }
        }

        // Continue to next step
        return $next($request);
    }
}
