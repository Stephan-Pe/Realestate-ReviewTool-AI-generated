<?php

error_log('===== POST TEST =====');

header('Content-Type: text/plain');

echo "POST TEST OK\n";
echo "METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n";
echo "QUERY: " . ($_SERVER['QUERY_STRING'] ?? '') . "\n";

$body = file_get_contents('php://input');

echo "BODY: " . $body . "\n";