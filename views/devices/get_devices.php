<?php
require_once '../lib/dao.php';
require_once '../lib/devices.php';

/// This file is responsible for handling device data requests.
/// It checks user permissions, retrieves device data from the database, and returns it in JSON format
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 0) {
    http_content_type('application/json');

    $result = [];
    try {
        $dao = DAO::getInstance();
        $results = $dao->read(new DEVICES(), false, true);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }

    if (!$results) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed']);
        exit;
    }
    if (count($results) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'No devices found']);
        exit;
    }
    echo json_encode($results);
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
