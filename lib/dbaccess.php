<?php
require_once __DIR__ . '/logger.php';

class DbSQL {
    // Database connection parameters
    // Update these parameters according to your database configuration
    private $conn;
    private $dbConfig;

    public function __construct($dbConfig) {
        $this->conn = null;
        $this->dbConfig = $dbConfig;
    }

    private function loadPhpDbConfig() {
        $filePath = __DIR__ . '/../conf/db_conf.php';
        if (!file_exists($filePath)) {
            return null;
        }

        $config = include $filePath;
        if (is_array($config)) {
            return $config;
        }

        if (function_exists('mfplist_db_conf')) {
            $functionConfig = mfplist_db_conf();
            if (is_array($functionConfig)) {
                return $functionConfig;
            }
        }

        return null;
    }

    private function getConfigValue($objectGetter, $arrayKeys) {
        if (is_object($this->dbConfig) && method_exists($this->dbConfig, $objectGetter)) {
            return $this->dbConfig->{$objectGetter}();
        }

        if (is_array($this->dbConfig)) {
            foreach ($arrayKeys as $key) {
                if (array_key_exists($key, $this->dbConfig)) {
                    return $this->dbConfig[$key];
                }
            }
        }

        $phpConfig = $this->loadPhpDbConfig();
        if (is_array($phpConfig)) {
            foreach ($arrayKeys as $key) {
                if (array_key_exists($key, $phpConfig)) {
                    return $phpConfig[$key];
                }
            }
        }

        return null;
    }

    public function open() {
        $this->conn = null;
        try {
            $host = $this->getConfigValue('getHost', array('host'));
            $dbName = $this->getConfigValue('getDbName', array('db_name', 'database'));
            $username = $this->getConfigValue('getUsername', array('username', 'user'));
            $password = $this->getConfigValue('getPassword', array('password', 'pass'));

            if (empty($host) || empty($dbName) || empty($username) || $password === null) {
                throw new InvalidArgumentException('Incomplete database credentials.');
            }

            $this->conn = new PDO(
                'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4',
                $username,
                $password
            );
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            Logger::safeError('Database connection failed.', array('exception' => $e->getMessage()));
            return null;
        } catch (Exception $e) {
            Logger::safeError('Database configuration error.', array('exception' => $e->getMessage()));
            return null;
        }
        return $this->conn;
    }

    public function close() {
        $this->conn = null;
    }
}


