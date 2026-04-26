<?php
require_once '../lib/dao.php';
require_once '../lib/contacts.php';

/// This file is responsible for handling contact creation requests.
/// It checks user permissions, decodes the JSON input, and creates a new contact in the database.

if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    http_content_type('application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $dao = DAO::getInstance();
    $contact = new CONTACTS();
    try {
        $contact->fromJson($data);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON structure']);
        exit;
    }

    if ($dao->create($contact)) {
        http_response_code(201);
        echo json_encode(['message' => 'Contact created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed']);
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
}
