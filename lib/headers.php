<?php

require_once 'logger.php';
require_once 'client_apps_manager.php';

class Headers {

    /**
     * Set CORS headers based on the allowed origins list from config.
     * Handles preflight OPTIONS requests automatically.
     */
    static function setCorsHeaders() {
        //retrieve app id from headers:
        $appUuid = self::getClientAppUuid();
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
   
    // Function to get the client UUID from the request headers
    public static function getClientAppUuid(){ 
        $appUuid = "";

        //Ref to X-APP-ID in request headers (case-insensitive)
        if (isset($_SERVER['HTTP_X_APP_ID'])) {// -> X-APP-ID
            $appUuid = trim($_SERVER['HTTP_X_APP_ID']);// -> X-APP-ID
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['X-App-Id'])) {//X-App-Id
                $appUuid = trim($requestHeaders['X-App-Id']);//X-App-Id
            }
        } else {
            Logger::safeError("No app UUID provided in the request headers.");
        }


        return $appUuid;
    }

    // Function to get the Bearer token from the Authorization header
    public static function getBearerToken() {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        // Header value found
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

}
