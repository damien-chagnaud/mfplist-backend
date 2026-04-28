<?php

// ##################################################
require_once '../lib/credentials.php';
require_once '../lib/headers.php';

function isPublicRoute() {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($requestPath) || $requestPath === '') {
        $requestPath = '/';
    }

    $requestPath = rtrim($requestPath, '/');
    if ($requestPath === '') {
        $requestPath = '/';
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $publicRoutes = array(
        'GET' => array('/', '/login'),
        'POST' => array('/login')
    );

    return in_array($requestPath, $publicRoutes[$method] ?? array(), true);
}


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
    if (isPublicRoute() || getenv('DEBUG_MODE') === 'true') {
        $response_code = 200;
        $quit = false;
    } else {
        $response_code = 401;
        $response_text = 'Unauthorized';
    }
}

//("Content-Type: application/json; charset=UTF-8", true, $response_code);

if ($response_code !== 200 || $quit) {
    echo json_encode(['error' => $response_text]);
    exit();
}




