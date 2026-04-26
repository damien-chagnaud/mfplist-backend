<?php
include 'head.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['USER_LEVEL'] > 0) {
    $result = [];

    $result["user_name"] = $_SERVER['USER_NAME'];
    $result["user_id"] = $_SERVER['USER_ID'];
    $result["user_level"] = $_SERVER['USER_LEVEL'];
    $result["message"] = "successful" ;
    echo json_encode($result);
}