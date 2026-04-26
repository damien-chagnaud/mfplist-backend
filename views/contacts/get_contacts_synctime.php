<?php
require_once '../lib/dao.php';
require_once '../lib/timestamp.php';

// this file is used to get the synchronization time for contacts
// it returns the last refresh time in a JSON format

if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 0) {
    http_content_type('application/json');
    
    $timestamp = new TIMESTAMP();
    $timestamp->setName(CONTACTS::getTableName());
    $result = DAO::getInstance()->read($timestamp, false, true);

    if (!$result) {
        
        http_response_code(500);
        echo json_encode(['error' => 'Failed to read timestamp']);
        exit;
    }
    $refresh = $result[0]['refresh'] ?? null;
    $refreshTime = strtotime($refresh);
    echo "{\"synctime\":\"$refreshTime\"}";
}else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
