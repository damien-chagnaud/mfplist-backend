<?php
require_once '../lib/credentials.php';

$contenType = filter_input(INPUT_SERVER , 'CONTENT_TYPE',FILTER_SANITIZE_STRING);
$verify = false;
$response_code = 200;
header("Cache-Control: no-cache");

try{
    if (str_contains($contenType,'application/json')) {
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $email = (isset($data->email))? $data->email: '';
        $password = (isset($data->password))? $data->password: '';
        $verify = true; 
    }
} catch (Exception $e) {
    $response_code = 400;
}

if($verify) {
    $cred = new Credentials();
    $uid = $cred->checkCredentials($email, $password);
    if($uid!==false){
        $token = $cred->generateToken($uid);
        echo json_encode(['message' => 'successful', 'token' => $token, 'user_id' => $uid]);
        $response_code = 200;
    } else {
        echo json_encode(['message' => 'failed']);
        $response_code = 401;
    }

} else {
    $response_code = 420;
}

header("Content-Type: application/json; charset=UTF-8", true, $response_code);