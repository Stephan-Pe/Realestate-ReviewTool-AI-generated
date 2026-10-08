<?php

error_log('===== REWRITE TEST =====');

header('Content-Type: text/plain');

echo "REWRITE TEST OK\n";
echo "METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? '') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? '') . "\n";

$body = file_get_contents('php://input');

echo "BODY: " . $body . "\n";