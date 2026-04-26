<?php

$response_code = 500;

if($_SERVER['SECURED'] === true) {
    $result["user_name"] = $_SERVER['USER_NAME'];
    $result["user_id"] = $_SERVER['USER_ID'];
    $result["user_level"] = $_SERVER['USER_LEVEL'];
    $result["message"] = "successful" ;
    echo json_encode($result);
    $response_code = 200;
}else {
    $result["message"] = "Access denied";
    echo json_encode($result);
    $response_code = 403;
}

header("Content-Type: application/json; charset=UTF-8", true, $response_code);