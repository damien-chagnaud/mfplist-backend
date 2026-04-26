<?php

// This file contains the DbConfig class which provides database configuration.
class DbConfig {
    private $host;
    private $db_name;
    private $username;
    private $password;

    function __construct($host, $db_name, $username, $password) {
        $this->host = $host;
        $this->db_name = $db_name;
        $this->username = $username;
        $this->password = $password;
    }

    public function getHost() {
        return $this->host;
    }

    public function getDbName() {
        return $this->db_name;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }



}

