<?php
require_once '../lib/dao.php';
require_once '../lib/contacts.php';

/// This file handles PUT requests to update existing contacts by UUID.

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

    try {
        if ($dao->updateByUUID($contact)) {
            http_response_code(200);
            echo json_encode(['message' => 'Contact updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }

} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}