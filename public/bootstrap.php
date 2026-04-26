<?php
require_once '../lib/config.php';

/**
 * This file is responsible for bootstrapping the application.
 * It loads the configuration, and initializes the application.
 */

//load configuration
$appConfig = loadAppConfig();

function envOrDefault($name, $default = null)
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

if ($appConfig === false) {
    $response_code = 500;
    $response_text = 'Internal Server Error';
    header("Content-Type: application/json; charset=UTF-8", true, $response_code);
    echo json_encode(['error' => $response_text]);
    exit();
}else {
    // Set the application name
    if (!isset($appConfig['app_name']) || empty($appConfig['app_name'])) {
        throw new Exception("Application name is not set in the configuration file.");
    } else {
        $appName = $appConfig['app_name'];
        $_SERVER['APP_NAME'] = $appName;
    }
    
    // Set the site URL
    if (!isset($appConfig['site_url']) || empty($appConfig['site_url'])) {
        throw new Exception("Site URL is not set in the configuration file.");
    }else{
        $siteURL = $appConfig['site_url'];
        $_SERVER['SITE_URL'] = $siteURL;
    }

    // Set DB configuration
    if (!isset($appConfig['db_conf']) || empty($appConfig['db_conf'])) {
        throw new Exception("Database configuration is not set in the configuration file.");
    } else {

        $dbConfig = $appConfig['db_conf'];
        $dbHost = envOrDefault('MFPLIST_DB_HOST', $dbConfig['host'] ?? null);
        $dbName = envOrDefault('MFPLIST_DB_NAME', $dbConfig['db_name'] ?? null);
        $dbUser = envOrDefault('MFPLIST_DB_USER', $dbConfig['username'] ?? null);
        $dbPass = envOrDefault('MFPLIST_DB_PASSWORD', $dbConfig['password'] ?? null);

        if (empty($dbHost) || empty($dbName) || empty($dbUser) || empty($dbPass)) {
            throw new Exception("Incomplete database configuration.");
        }

        configuration::$dbConfig = new DbConfig(
            $dbHost,
            $dbName,
            $dbUser,
            $dbPass
        );
    }
   
}

/**
 * Load the application configuration from a JSON file.
 *
 * @return array The application configuration settings.
 * @throws Exception If the configuration file does not exist or cannot be parsed.
 */
function loadAppConfig()
{
    $config = [];
    
    $file = '../conf/app_conf.json';
    if (file_exists($file)) {
        $string = file_get_contents($file);
        $config = json_decode($string, true);
        if ($config === null) {
            throw new Exception("Error parsing configuration file: $file");
        }
    } else {
        throw new Exception("Configuration file not found: $file");
    }

     // Load database configuration from PHP file
    $dbConfFile = '../conf/db_conf.php';
    if (file_exists($dbConfFile)) {
        $dbConf = include $dbConfFile;
        if (is_array($dbConf)) {
            $config['db_conf'] = $dbConf;
        }
    }else {
        throw new Exception("Database configuration file not found: $dbConfFile");
    }
        
    return $config;

}

/**
 * Polyfill for filter_string function.
 * This function sanitizes a string by removing null bytes and HTML tags,
 * and escaping single and double quotes.
 *
 * @param string $string The input string to sanitize.
 * @return string The sanitized string.
 */
function filter_string_polyfill(string $string): string
{
    $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
    return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
}