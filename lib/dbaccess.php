<?php

class DbSQL {
    // Database connection parameters
    // Update these parameters according to your database configuration
    private $conn;
    private $dbConfig;

    public function __construct($dbConfig) {
        $this->conn = null;
        $this->dbConfig = $dbConfig;
    }

    public function open() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->dbConfig->getHost() .';dbname=' . $this->dbConfig->getDbName(),
                 $this->dbConfig->getUsername(),
                  $this->dbConfig->getPassword()
            );
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log('Database connection failed.');
            return null;
        }
        return $this->conn;
    }

    public function close() {
        $this->conn = null;
    }
}


