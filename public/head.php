<?php

// ##################################################
require_once '../lib/credentials.php';
require_once '../lib/headers.php';

function isHttpsRequest() {
    $https = $_SERVER['HTTPS'] ?? '';
    if (is_string($https) && strtolower($https) !== 'off' && $https !== '') {
        return true;
    }

    $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    if (is_string($forwardedProto) && strtolower($forwardedProto) === 'https') {
        return true;
    }

    $forwardedSsl = $_SERVER['HTTP_X_FORWARDED_SSL'] ?? '';
    return is_string($forwardedSsl) && strtolower($forwardedSsl) === 'on';
}

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
    if (!isHttpsRequest()) {
        $cred->deleteToken($token);
        $_SERVER['SECURED'] = false;
        $response_code = 403;
        $response_text = 'Insecure connection';
    } else {
        $result = $cred->validToken($token);

        if($result) {
            $response_code = 200;
            $quit = false;
            $_SERVER['SECURED'] = true;
            $_SERVER['USER_TOKEN'] = $token;
            $user = $cred->getUser($token); 
            $_SERVER['USER_LEVEL'] = $user['level'];
            $_SERVER['USER_NAME'] = $user['username'];
            $_SERVER['USER_ID'] = $user['uid'];
        } else {
            $response_code = 401;
            $response_text = 'Unauthorized';
        }

        if (!isset($_SERVER['SECURED'])) {
            $_SERVER['SECURED'] = false;
        }
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

if ($response_code !== 200 || $quit) {
    header("Cache-Control: no-cache");
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['error' => $response_text]);
    exit();
}




