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

    private function tokenDigest($token) {
        return hash('sha256', (string) $token);
    }

    private function upgradeLegacyPasswordHash($userId, $plainPassword) {
        $newHash = password_hash($plainPassword, PASSWORD_ARGON2ID);
        $query = 'UPDATE cred_users SET password = :password WHERE uid = :uid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $newHash);
        $stmt->bindParam(':uid', $userId);
        $stmt->execute();
    }

    public function __construct() {
        $dbConfig = configuration::$dbConfig;
        if ($dbConfig === null) {
            throw new Exception("Database configuration is not set.");
        }
        $dbAccess = new DbSQL($dbConfig);
        $this->conn = $dbAccess->open();
    }

    public function checkCredentials($email, $password) {
        $query = 'SELECT uid, password FROM cred_users WHERE email = :email';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        try {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }

        if (!$result) {
            return false;
        }

        $storedPassword = (string) $result['password'];

        if (password_verify($password, $storedPassword)) {
            return $result['uid'];
        }

        // Backward-compatible fallback for legacy SHA-512 hashes.
        $legacyPasswordHash = hash('sha512', $password);
        if (hash_equals($storedPassword, $legacyPasswordHash)) {
            $this->upgradeLegacyPasswordHash($result['uid'], $password);
            return $result['uid'];
        }

        return false;
    }

    public function generateToken($uuid) {
        $token = bin2hex(random_bytes(32));
        $tokenDigest = $this->tokenDigest($token);
        $query = 'UPDATE cred_users SET token = :token, token_created_at = NOW() WHERE uid = :uid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $tokenDigest);
        $stmt->bindParam(':uid', $uuid);
        $stmt->execute();

        return $token;
    }


    public function checkToken($token) {
        $tokenDigest = $this->tokenDigest($token);
        $query = 'SELECT * FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $tokenDigest);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteToken($token) {
        $tokenDigest = $this->tokenDigest($token);
        $query = 'UPDATE cred_users SET token = NULL WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $tokenDigest);
        $stmt->execute();
    }

    public function getUser($token) {
        $tokenDigest = $this->tokenDigest($token);
        $query = 'SELECT * FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $tokenDigest);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function isTokenExpired($token) {
        $tokenDigest = $this->tokenDigest($token);
        $query = 'SELECT token_created_at FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $tokenDigest);
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
        $tokenDigest = $this->tokenDigest($token);
        $query = 'SELECT level FROM cred_users WHERE token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $tokenDigest);
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
