<?php

/**
 * This file is responsible for bootstrapping the application.
 * It loads the configuration, and initializes the application.
 */

//load configuration
$appConfig = loadAppConfig();

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
        putenv('APP_NAME=' . $appConfig['app_name']);
    }
    
    // Set the site URL
    if (!isset($appConfig['site_url']) || empty($appConfig['site_url'])) {
        throw new Exception("Site URL is not set in the configuration file.");
    }else{
        putenv('SITE_URL=' . $appConfig['site_url']);
    }

    // Set DB configuration
    switch ($appConfig['database_system']) {
        case 'mariadb':
            putenv('DATABASE_SYSTEM=mariadb');
            putenv('MFPLIST_DB_HOST=' . ($appConfig['db_conf']['host'] ?? ''));
            putenv('MFPLIST_DB_NAME=' . ($appConfig['db_conf']['db_name'] ?? ''));
            putenv('MFPLIST_DB_USER=' . ($appConfig['db_conf']['username'] ?? ''));
            putenv('MFPLIST_DB_PASSWORD=' . ($appConfig['db_conf']['password'] ?? ''));
            break;
        case 'sqlite':
            putenv('DATABASE_SYSTEM=sqlite');
            putenv('MFPLIST_DB_FILE=' . ($appConfig['db_conf']['file'] ?? ''));
            // SQLite configuration can be handled here if needed
            break;
        default:
            throw new Exception("Unsupported database system: " . $appConfig['database_system']);
    }

    //Set debug mode
    if (isset($appConfig['debug'])) {
        putenv('DEBUG_MODE=' . ($appConfig['debug'] ? 'true' : 'false'));
    } else {
        putenv('DEBUG_MODE=false');
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

    // Set the database system
    if (!isset($config['database_system']) || empty($config['database_system'])) {
        throw new Exception("Database system is not set in the configuration file.");
    }else {
        $_SERVER['DATABASE_SYSTEM'] = $config['database_system'];
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

function envOrDefault($name, $default = null)
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}