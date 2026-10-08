<?php

error_log('===== INDEX HIT =====');

header('Content-Type: text/plain');

echo "INDEX OK\n";
echo "METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n";
echo "URI: " . ($_SERVER['REQUEST_URI'] ?? '') . "\n";
echo "QUERY: " . ($_SERVER['QUERY_STRING'] ?? '') . "\n";

exit;