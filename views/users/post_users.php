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

/// This file is responsible for handling user creation requests.
/// It checks user permissions, decodes the JSON input, and creates a new user in the database.
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

    if (!isset($data['password']) || $data['password'] === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Password is required']);
        exit;
    }

    $data['password'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);

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
        if ($dao->create($user)) {
            http_response_code(201);
            echo json_encode(['message' => 'User created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        Logger::safeError('post_users failed.', array('exception' => $e->getMessage()));
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
