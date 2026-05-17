<?php

//Load DAO classes based on the configured database system
if($_SERVER['DATABASE_SYSTEM']=='mariadb'){
    require_once '../lib/dao_mariadb.php';
}else if($_SERVER['DATABASE_SYSTEM']=='sqlite'){
    require_once '../lib/dao_sqlite.php';
}

require_once '../lib/logger.php';
require_once '../dao/users.dao.php';
require_once '../lib/dao_tools.php';


//check if app Client is authorized to access data
/// This file is responsible for handling users data requests.
/// It checks user permissions, retrieves users data from the database, and returns it in JSON format.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    header('Content-Type: application/json; charset=UTF-8');

    if (!DaoTools::checkAppAccess(['UsersDao'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden: App does not have access to Users data']);
        exit;
    }

    try {
        $dao = DAO::getInstance();
        $results = $dao->read(new UsersDao(), false, true);
    } catch (Exception $e) {
        http_response_code(500);
        Logger::safeError('get_users failed.', array('exception' => $e->getMessage()));
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
        echo json_encode(['error' => 'No users found']);
        exit;
    }

    // Never expose password hashes or session tokens in list responses.
    $safeResults = array_map(function ($row) {
        unset($row['password'], $row['token']);
        return $row;
    }, $results);

    echo json_encode($safeResults);
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
