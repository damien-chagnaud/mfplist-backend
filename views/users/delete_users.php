<?php

//Load DAO classes based on the configured database system
if($_SERVER['DATABASE_SYSTEM']=='mariadb'){
    require_once '../lib/dao.mariadb.php';
}else if($_SERVER['DATABASE_SYSTEM']=='sqlite'){
    require_once '../lib/dao.sqlite.php';
}

require_once '../lib/logger.php';
require_once '../dao/users.dao.php';

/// This file is responsible for handling user deletion requests by UUID.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    header('Content-Type: application/json; charset=UTF-8');

    if (!isset($uuid) || trim((string) $uuid) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user uuid']);
        exit;
    }

    $lookup = new UsersDao();
    $lookup->setUuid((string) $uuid);

    try {
        $dao = DAO::getInstance();
        $results = $dao->read($lookup, false, true);
        if ($results === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
            exit;
        }

        if (count($results) === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        $toDelete = new UsersDao();
        $toDelete->setId($results[0]['id']);

        if ($dao->delete($toDelete)) {
            http_response_code(200);
            echo json_encode(['message' => 'User deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        Logger::safeError('delete_users failed.', array('exception' => $e->getMessage()));
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
