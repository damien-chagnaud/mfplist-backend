<?php
require_once '../lib/credentials.php';
require_once '../lib/logger.php';

$contenType = filter_input(INPUT_SERVER , 'CONTENT_TYPE',FILTER_SANITIZE_STRING);
$infos = [];
$response_code = 200;
header("Cache-Control: no-cache");
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['SECURED']) {
    $infos['user_level'] = $_SERVER['USER_LEVEL'] ;
    $infos['user_name'] = $_SERVER['USER_NAME'];
    $infos['user_id'] = $_SERVER['USER_ID'];
    $infos['valid'] = true;
}

http_response_code($response_code);
echo json_encode($infos);
