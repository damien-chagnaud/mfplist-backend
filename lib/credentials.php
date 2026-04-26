<?php
/**
 *  Credentials Class
 * 
 *  This class handles user authentication, token generation, and validation.
 *   @package Credentials
 *   @version 1.0
 */

include 'dbaccess.php';

Class Credentials{
    private $conn;

    public function __construct() {
        $dbConfig = configuration::$dbConfig;
        if ($dbConfig === null) {
            throw new Exception("Database configuration is not set.");
        }
        $dbAccess = new DbSQL($dbConfig);
        $this->conn = $dbAccess->open();
    }

    public function checkCredentials($email, $password) {
        $query = 'SELECT * FROM cred_users WHERE email = :email AND password = :password';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $hashedPassword = hash('sha512', $password);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();
        $result;

        try {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            return $result['uid'];
        } else {
            return false;
        }
    }

    public function generateToken($uuid) {
        $token = bin2hex(random_bytes(16));
        $query = 'UPDATE cred_users SET token = :token, token_created_at = NOW() WHERE uid = :uid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':uid', $uuid);
        $stmt->execute();

        return $token;
    }


    public function checkToken($token) {
        $query = 'SELECT * FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteToken($token) {
        $query = 'UPDATE cred_users SET token = NULL WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    }

    public function getUser($token) {
        $query = 'SELECT * FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function isTokenExpired($token) {
        $query = 'SELECT token_created_at FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $tokenCreatedAt = strtotime($result['token_created_at']);
            $currentTime = time();
            if (($currentTime - $tokenCreatedAt) > 3600) { // 3600 seconds = 1 hour
                return true;
            }
        }
        return false;
    }

    public function getUserLevel($token) {
        $query = 'SELECT level FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['level'] : null;
    }

    public function validToken($token) {// 0: token invalid, 1: valid token, 2: token expired, 3: insufficient level
        if ($this->checkToken($token)) {
            if ($this->isTokenExpired($token)) {
                $this->deleteToken($token);
                return 0;
            } else {
                $userLevel = $this->getUserLevel($token);
                return $userLevel;
            }
        } else {
            return 0;
        }
    }

    public function close() {
        $this->conn = null;
    }
}
?>
