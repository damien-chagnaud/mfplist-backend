<?php

/**
 * This file is responsible for bootstrapping the application.
 * It loads the configuration and initializes the application.
 */
require_once '../lib/logger.php';
require_once '../lib/headers.php';

//load configuration and set environment variables for the application and the database based on the configuration files and request headers.
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
        Logger::safeError("Application name is not set in the configuration file.");
        throw new Exception("Application name is not set in the configuration file.");
    } else {
        putenv('APP_NAME=' . $appConfig['app_name']);
    }
    
    // Set the site URL
    if (!isset($appConfig['site_url']) || empty($appConfig['site_url'])) {
        Logger::safeError("Site URL is not set in the configuration file.");
        throw new Exception("Site URL is not set in the configuration file.");
    }else{
        putenv('SITE_URL=' . $appConfig['site_url']);
    }

    // Set DB configuration
    switch ($appConfig['database_system']) {
        case 'mariadb':
            putenv('DATABASE_SYSTEM=mariadb');
            putenv('COPILOC_DB_HOST=' . ($appConfig['db_conf']['host'] ?? ''));
            putenv('COPILOC_DB_NAME=' . ($appConfig['db_conf']['db_name'] ?? ''));
            putenv('COPILOC_DB_USER=' . ($appConfig['db_conf']['username'] ?? ''));
            putenv('COPILOC_DB_PASSWORD=' . ($appConfig['db_conf']['password'] ?? ''));
            break;
        case 'sqlite':
            putenv('DATABASE_SYSTEM=sqlite');
            putenv('MFPLIST_DB_FILE=' . ($appConfig['db_conf']['file'] ?? ''));
            // SQLite configuration can be handled here if needed
            break;
        default:
            Logger::safeError("Unsupported database system: " . $appConfig['database_system'], array('DATABASE_SYSTEM' => $appConfig['database_system']));
            throw new Exception("Unsupported database system: " . $appConfig['database_system']);
    }

    //Set debug mode
    if (isset($appConfig['debug'])) {
        putenv('DEBUG_MODE=' . ($appConfig['debug'] ? 'true' : 'false'));
    } else {
        putenv('DEBUG_MODE=false');
    }
   
}


//FUNCTIONS:


/**
 * Load the application configuration and database configuration from the respective PHP files.
 *
 * @return array The application configuration settings.
 */
function loadAppConfig()
{
    $config = [];
    
    // Load application configuration from PHP file:
    $appConfigFile = '../conf/app_conf.php';
    if (file_exists($appConfigFile)) {
        $appConfig = include $appConfigFile;
        if (is_array($appConfig)) {
            $config = array_merge($config, $appConfig);
        }
    } else {
        Logger::safeError("Application configuration file not found: $appConfigFile");
        throw new Exception("Application configuration file not found: $appConfigFile");
    }
    
    // Set the database system
    if (!isset($config['database_system']) || empty($config['database_system'])) {
        Logger::safeError("Database system is not set in the configuration file.");
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
        Logger::safeError("Database configuration file not found: $dbConfFile");
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