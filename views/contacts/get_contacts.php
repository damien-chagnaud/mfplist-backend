<?php
require_once '../lib/dao.php';
require_once '../lib/contacts.php';

/// This file is responsible for handling contact data requests.
/// It checks user permissions, retrieves contact data from the database, and returns it in JSON format.

if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 0) {
    http_content_type('application/json');

    // The request is using the GET method
    $dao = DAO::getInstance();
    $results = $dao->read(new CONTACTS(), false, true);
    if (!$results) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed']);
        exit;
    }
    echo json_encode($results);
    exit;
}else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
