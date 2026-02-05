<?php
$host = 'phpstack-1419372-6088683.cloudwaysapps.com';
$port = 8082;
$timeout = 10;

echo "Testing connection to $host:$port...\n";

$start = microtime(true);
$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
$end = microtime(true);

if ($fp) {
    echo "SUCCESS: Connection established in " . round(($end - $start) * 1000) . "ms\n";
    fclose($fp);
} else {
    echo "ERROR: Connection failed: $errstr ($errno)\n";
    echo "Time taken: " . round(($end - $start) * 1000) . "ms\n";
}
