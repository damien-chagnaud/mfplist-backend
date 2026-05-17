<?php
require_once __DIR__ . '/logger.php';

class DbAccess {
    // Database connection parameters
    // Update these parameters according to your database configuration
    private $conn;

    public function __construct() {
        $this->conn = null;
    }

    public function open() {
        $this->conn = null;
        try {
            switch (envOrDefault('DATABASE_SYSTEM')) {
                case 'mariadb':
                    $this->conn = $this->connectMariaDB();
                    break;
                case 'sqlite':
                    $this->conn = $this->connectSQLite();
                    break;
                default:
                    Logger::safeError('Unsupported database system specified.', array('DATABASE_SYSTEM' => envOrDefault('DATABASE_SYSTEM')));
                    throw new Exception("Unsupported database system: " . envOrDefault('DATABASE_SYSTEM', 'unknown'));
            }
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Logger::safeError("Database connection error: " . $e->getMessage());
            throw new Exception("Failed to connect to the database.");  
        }
       
        return $this->conn;
    }

    public function close() {
        $this->conn = null;
    }

    private function connectMariaDB() {
        $host = envOrDefault('COPILOC_DB_HOST');
        $dbName = envOrDefault('COPILOC_DB_NAME');
        $username = envOrDefault('COPILOC_DB_USER');
        $password = envOrDefault('COPILOC_DB_PASSWORD');

        if (empty($host) || empty($dbName) || empty($username) || $password === null) {
            Logger::safeError('Incomplete database credentials for MariaDB.', array('host' => $host, 'dbName' => $dbName, 'username' => $username));
            throw new InvalidArgumentException('Incomplete database credentials for MariaDB.');
        }

        return new PDO(
            'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4',
            $username,
            $password
        );
    }

    private function connectSQLite() {
        $file = $this->envOrDefault('MFPLIST_DB_FILE');

        if (empty($file)) {
            Logger::safeError('SQLite database file path is not set in environment variables.');
            throw new InvalidArgumentException('Incomplete database credentials for SQLite.');
        }

        return new PDO('sqlite:' . $file);
    }

    private function envOrDefault($name, $default = null){
        $value = getenv($name);
        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }
}



