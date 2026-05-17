<?php

//Load DAO classes based on the configured database system
if($_SERVER['DATABASE_SYSTEM']=='mariadb'){
    require_once '../lib/dao.mariadb.php';
}else if($_SERVER['DATABASE_SYSTEM']=='sqlite'){
    require_once '../lib/dao.sqlite.php';
}

require_once '../lib/logger.php';
require_once '../dao/users.dao.php';
require_once '../lib/dao_tools.php';

/// This file is responsible for handling user update requests.
/// It checks user permissions, processes the incoming JSON data, and updates the user in the database.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    header('Content-Type: application/json; charset=UTF-8');

    if (!DaoTools::checkAppAccess(['UsersDao'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden: App does not have access to Users data']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON structure']);
        exit;
    }

    if (array_key_exists('password', $data) && $data['password'] !== null && $data['password'] !== '') {
        $data['password'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
    }

    $dao = DAO::getInstance();
    $user = new UsersDao();

    try {
        $user->fromJson($data);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user payload']);
        exit;
    }

    try {
        if ($dao->updateByUUID($user)) {
            http_response_code(200);
            echo json_encode(['message' => 'User updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        Logger::safeError('put_users failed.', array('exception' => $e->getMessage()));
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
