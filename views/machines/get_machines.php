<?php

//Load DAO classes based on the configured database system
if($_SERVER['DATABASE_SYSTEM']=='mariadb'){
    require_once '../lib/dao.mariadb.php';
}else if($_SERVER['DATABASE_SYSTEM']=='sqlite'){
    require_once '../lib/dao.sqlite.php';
}

require_once '../lib/logger.php';
require_once '../dao/machine.dao.php';

/// This file is responsible for handling machine data requests.
/// It checks user permissions, retrieves machine data from the database, and returns it in JSON format.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 0) {
    header('Content-Type: application/json; charset=UTF-8');

    try {
        $dao = DAO::getInstance();
        $results = $dao->read(new MachineDao(), false, true);
    } catch (Exception $e) {
        http_response_code(500);
        Logger::safeError('get_machines failed.', array('exception' => $e->getMessage()));
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }

    if (!$results) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed']);
        exit;
    }

    if (count($results) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'No machines found']);
        exit;
    }

    echo json_encode($results);
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
