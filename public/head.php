<?php

// ##################################################
require_once '../lib/credentials.php';
require_once '../lib/headers.php';


// Get the Bearer token
$token = Headers::getBearerToken();

$response_code = 400;
$response_text = 'Bad Request';
$quit = true;

// Check if the token is valid
if ($token) {
    $cred = new Credentials();
    $result = $cred->validToken($token);

    if($result) {
        $response_code = 200;
        $quit = false;
        $_SERVER['SECURED'] = true;
        $_SERVER['USER_LEVEL'] = $result;
        $_SERVER['USER_TOKEN'] = $token;
        $user = $cred->getUser($token); 
        $_SERVER['USER_NAME'] = $user['username'];
        $_SERVER['USER_ID'] = $user['uid'];
    } else {
        $response_code = 401;
        $response_text = 'Unauthorized';
    }

    if (!isset($_SERVER['SECURED'])) {
        $_SERVER['SECURED'] = false;
    }
} else {
    $_SERVER['SECURED'] = false;
    $response_code = 200;
    $quit = false;
}

//("Content-Type: application/json; charset=UTF-8", true, $response_code);

if ($response_code !== 200 || $quit) {
    echo json_encode(['error' => $response_text]);
    exit();
}




