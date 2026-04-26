<?php
require_once '../lib/dao.php';
require_once '../lib/parts.php';

/// This file is responsible for handling part update requests.
/// It checks user permissions, processes the incoming JSON data, and updates the part in the database
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    http_content_type('application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $dao = DAO::getInstance();
    $part = new PARTS();

    try {
        $part->fromJson($data);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON structure']);
        exit;
    }

    try {
        if ($dao->updateByUUID($part)) {
            http_response_code(200);
            echo json_encode(['message' => 'Part updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log('put_parts update failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }

} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}