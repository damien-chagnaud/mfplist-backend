<?php
require_once '../lib/dao.php';
require_once '../lib/timestamp.php';

/// This file is responsible for handling device synchronization time requests.
/// It checks user permissions, retrieves the synchronization time from the database,
/// and returns it in JSON format.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 0) {
    http_content_type('application/json');

    $timestamp = new TIMESTAMP();
    $timestamp->setName(DEVICES::getTableName());
    $result = DAO::getInstance()->read($timestamp, false, true);

    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to read timestamp']);
        exit;
    }
    if (count($result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'No timestamp found']);
        exit;
    }

    $refresh = $result[0]['refresh'] ?? null;
    $refreshTime = strtotime($refresh);
    echo "{\"synctime\":\"$refreshTime\"}";
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}