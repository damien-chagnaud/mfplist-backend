<?php
require_once '../lib/credentials.php';
require_once '../lib/logger.php';

$contenType = filter_input(INPUT_SERVER , 'CONTENT_TYPE',FILTER_SANITIZE_STRING);
$verify = false;
$response_code = 200;
header("Cache-Control: no-cache");
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['SECURED'] && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
    $verify = true;
}

http_response_code($response_code);
echo json_encode(['valid' => $verify]);
