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

/// This file is responsible for handling single user data requests by UUID.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    header('Content-Type: application/json; charset=UTF-8');

    if (!DaoTools::checkAppAccess(['UsersDao'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden: App does not have access to Users data']);
        exit;
    }

    if (!isset($uuid) || trim((string) $uuid) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user uuid']);
        exit;
    }

    $user = new UsersDao();
    $user->setUuid((string) $uuid);

    try {
        $dao = DAO::getInstance();
        $results = $dao->read($user, false, true);
    } catch (Exception $e) {
        http_response_code(500);
        Logger::safeError('get_users_by_uuid failed.', array('exception' => $e->getMessage()));
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }

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

    $row = $results[0];
    unset($row['password'], $row['token']);

    echo json_encode($row);
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
