<?php

// ##################################################
require_once '../lib/credentials.php';
require_once '../lib/headers.php';

function isHttpsRequest() {
    if(getenv('DEBUG_MODE') === 'true') {
        return true;
    }
    
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

/**
 * Set CORS headers based on the allowed origins list from config.
 * Handles preflight OPTIONS requests automatically.
 */
function setCorsHeaders() {
    //retrieve app id from headers:
    $appUuid = Headers::getClientAppUuid();
    if($appUuid=== null) {
        Logger::warning("No app UUID provided in the request headers.");
    }

    // Retrieve client app information based on the app UUID and set CORS headers accordingly
    $clientApp = null;
    try{
        $clientAppsManager = new ClientAppsManager();
        $clientApp = $clientAppsManager->getClientsByUuid($appUuid);
    } catch (Exception $e) {
        Logger::warning("Failed to retrieve client app information.", array('app_uuid' => $appUuid, 'exception' => $e->getMessage()));
    }

    // If no client app found, log the error and respond with 403 Forbidden
    if ($clientApp === null) {
        Logger::info("No client reference found for the provided app UUID.", array('app_uuid' => $appUuid));
        http_response_code(403);
        header('Access-Control-Allow-Origin:null');
        header("Content-Type: application/json; charset=UTF-8", true, 403);
        echo json_encode(['error' => 'Forbidden: no client reference found']);
        exit();
    }else {
        // Set environment variables for the app based on the client app configuration
        putenv('APP_UUID=' . $clientApp['uuid']);
        putenv('APP_NAME=' . $clientApp['name']);
        putenv('APP_ALLOWED_ORIGIN=' . $clientApp['allowed_origin']);
        putenv('APP_DAO=' . implode(',', $clientApp['access_dao']));
        header('Access-Control-Allow-Origin: ' . $clientApp['allowed_origin']);
    } 

    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept');
    header('Access-Control-Max-Age: 86400');

    // Respond to preflight requests immediately
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit();

    }

    // Set the Vary header to indicate that the response may vary based on the Origin header
    header('Vary: Origin');

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

// Apply CORS headers
setCorsHeaders();




