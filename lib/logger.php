<?php

/**
 * @author  advename
 * @since   October 27, 2019
 * @link    https://github.com/advename/Simple-PHP-Logger
 * @license MIT
 * @version 1.0.0
 * 
 * Description:
 * The simple php logger is a single-file logwriter with the features of:
 * - single file
 * - singleton pattern
 * - six log levels (info, notice, debug, warning, error, fatal)
 * - logs the line where the Logger method is executed (good for troubleshooting)
 * - logs the relative filepath of the source file, not the required one (good for troubleshooting)
 *
 */

class Logger
{

    /**
     * $log_file - path and log file name
     * @var string
     */
    protected static $log_file;

    /**
     * $file - file
     * @var string
     */
    protected static $file;

    /**
     * $options - settable options
     * @var array $dateFormat of the format used for the log.txt file; $logFormat used for the time of a single log event
     */
    protected static $options = [
        'dateFormat' => 'd-M-Y',
        'logFormat' => 'H:i:s d-M-Y'
    ];

    /**
     * @var string
     */
    protected static $log_directory;

    private static $instance;

    /**
     * Create the log file
     * @param string $log_file - path and filename of log
     * @param array $params - settable options
     */
    public static function createLogFile()
    {
        $time = date(static::$options['dateFormat']);
        static::$log_directory = dirname(__DIR__) . '/logs';
        static::$log_file = static::$log_directory . "/log-{$time}.txt";

        //Check if directory /logs exists
        if (!file_exists(static::$log_directory)) {
            throw new Exception('ERROR: Unable to loacte log directory.', 1);
        }

        //Create log file if it doesn't exist.
        if (!file_exists(static::$log_file)) {
            $createdFile = fopen(static::$log_file, 'a');
            if ($createdFile === false) {
                throw new Exception('ERROR: Unable to create log file.', 1);
            }

            fclose($createdFile);
            @chmod(static::$log_file, 0640);
        }

        //Check permissions of file.
        if (!is_writable(static::$log_file)) {
            //throw exception if not writable
            throw new Exception("ERROR: Unable to write to file!", 1);
        }
    }

    /**
     * Set logging options (optional)
     * @param array $options Array of settable options
     * 
     * Options:
     *  [
     *      'dateFormat' => 'value of the date format the .txt file should be saved int'
     *      'logFormat' => 'value of the date format each log event should be saved int'
     *  ]
     */
    public static function setOptions($options = [])
    {
        static::$options = array_merge(static::$options, $options);
    }

    /**
     * Info method (write info message)
     * 
     * Used for e.g.: "The user example123 has created a post".
     * 
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function info($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'INFO',
            'context' => $context
        ]);
    }

    /**
     * Notice method (write notice message)
     * 
     * Used for e.g.: "The user example123 has created a post".
     * 
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function notice($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'NOTICE',
            'context' => $context
        ]);
    }

    /**
     * Debug method (write debug message)
     * 
     * Used for debugging, could be used instead of echo'ing values
     * 
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function debug($message, array $context = [])
    {

        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'DEBUG',
            'context' => $context
        ]);
    }

    /**
     * Warning method (write warning message)
     * 
     * Used for warnings which is not fatal to the current operation
     * 
     * @param string $message Descriptive text of the warning
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function warning($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'WARNING',
            'context' => $context
        ]);
    }

    /**
     * Error method (write error message)
     * 
     * Used for e.g. file not found,...
     * 
     * @param string $message Descriptive text of the error
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function error($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'ERROR',
            'context' => $context
        ]);
    }

    /**
     * Safe logger wrapper for error-level events.
     * Falls back to PHP error_log on logger failures.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function safeError($message, array $context = [])
    {
        try {
            static::error($message, $context);
        } catch (Throwable $e) {
            error_log('[LOGGER_FAILURE] ' . $message);
        }
    }

    /**
     * Fatal method (write fatal message)
     * 
     * Used for e.g. database unavailable, system shutdown
     * 
     * @param string $message Descriptive text of the error
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function fatal($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'FATAL',
            'context' => $context
        ]);
    }

    /**
     * Write to log file
     * @param array $args Array of message (for log file), line (of log method execution), severity (for log file) and displayMessage (to display on frontend for the used)
     * @return void
     */
    // public function writeLog($message, $line = null, $displayMessage = null, $severity)
    public static  function writeLog($args = [])
    {
        //Create the log file
        static::createLogFile();

        // open log file
        if (!is_resource(static::$file)) {
            static::openLog();
        }

        // // grab the url path
        // $path = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

        //Grab time - based on the time format
        $time = date(static::$options['logFormat']);

        $contextData = $args['context'] ?? [];
        if (!is_array($contextData)) {
            $contextData = ['value' => (string) $contextData];
        }

        $safeContext = static::redactContext($contextData);
        $context = json_encode($safeContext, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($context === false) {
            $context = '{"error":"context_encode_failed"}';
        }

        $trace = $args['bt'] ?? [];
        $caller = is_array($trace) && count($trace) > 0 ? array_shift($trace) : [];
        $btLine = $caller['line'] ?? null;
        $btPath = $caller['file'] ?? null;

        // Convert absolute path to relative path (using UNIX directory seperators)
        $path = $btPath ? static::absToRelPath($btPath) : null;

        // Create log variable = value pairs
        $timeLog = is_null($time) ? "[N/A] " : "[{$time}] ";
        $pathLog = is_null($path) ? "[N/A] " : "[{$path}] ";
        $lineLog = is_null($btLine) ? "[N/A] " : "[{$btLine}] ";
        $severityLog = empty($args['severity']) ? "[N/A]" : "[" . static::sanitizeLogString($args['severity']) . "]";
        $messageLog = is_null($args['message'] ?? null) ? "N/A" : static::sanitizeLogString((string) $args['message']);
        $contextLog = empty($safeContext) ? "" : static::sanitizeLogString($context);

        $lineToWrite = "{$timeLog}{$pathLog}{$lineLog}: {$severityLog} - {$messageLog} {$contextLog}" . PHP_EOL;

        // Write time, url, & message to end of file
        if (flock(static::$file, LOCK_EX)) {
            fwrite(static::$file, $lineToWrite);
            fflush(static::$file);
            flock(static::$file, LOCK_UN);
        } else {
            throw new Exception('ERROR: Unable to lock log file.', 1);
        }

        // Close file stream
        static::closeFile();
    }

    /**
     * Open log file
     * @return void
     */
    private static function openLog()
    {
        $openFile = static::$log_file;
        // 'a' option = place pointer at end of file
        static::$file = fopen($openFile, 'a');
        if (static::$file === false) {
            throw new Exception("ERROR: Can't open {$openFile}!", 1);
        }
    }

    /**
     *  Close file stream
     */
    public static function closeFile()
    {
        if (static::$file) {
            fclose(static::$file);
        }
    }

    /**
     * Convert absolute path to relative url (using UNIX directory seperators)
     * 
     * E.g.:
     *      Input:      D:\development\htdocs\public\todo-list\index.php
     *      Output:     localhost/todo-list/index.php
     * 
     * @param string Absolute directory/path of file which should be converted to a relative (url) path
     * @return string Relative path
     */
    public static function absToRelPath($pathToConvert)
    {
        if (!$pathToConvert) {
            return 'cli';
        }

        $pathAbs = str_replace(['/', '\\'], '/', $pathToConvert);
        $documentRoot = str_replace(['/', '\\'], '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
        $serverName = $_SERVER['SERVER_NAME'] ?? 'cli';

        return $serverName . str_replace($documentRoot, '', $pathAbs);
    }

    private static function sanitizeLogString($value)
    {
        $normalized = str_replace(["\r", "\n", "\t"], ['\\r', '\\n', '\\t'], $value);
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $normalized);
    }

    private static function redactContext(array $context)
    {
        $sensitiveKeys = ['password', 'token', 'authorization', 'cookie', 'secret', 'api_key', 'apikey'];
        $redacted = [];

        foreach ($context as $key => $value) {
            $keyString = is_string($key) ? strtolower($key) : (string) $key;
            if (in_array($keyString, $sensitiveKeys, true)) {
                $redacted[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $redacted[$key] = static::redactContext($value);
            } elseif (is_string($value)) {
                $redacted[$key] = static::sanitizeLogString($value);
            } else {
                $redacted[$key] = $value;
            }
        }

        return $redacted;
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    { }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __destruct()
    { }
}