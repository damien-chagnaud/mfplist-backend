<?php
require_once    '../lib/dao.php';
require_once    '../lib/devices.php';

/// This file is responsible for handling device creation requests.
/// It checks user permissions, decodes the JSON input, and creates a new device in the database.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 0) {
    http_content_type('application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }

    $device = new DEVICES();
    $device->setName($data['name'] ?? '');
    $device->setType($data['type'] ?? '');
    $device->setStatus($data['status'] ?? '');

    try {
        $dao = DAO::getInstance();
        $dao->create($device);
        http_response_code(201);
        echo json_encode(['success' => 'Device created']);
    } catch (Exception $e) {
        http_response_code(500);
        error_log('post_devices failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Internal server error']);
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
