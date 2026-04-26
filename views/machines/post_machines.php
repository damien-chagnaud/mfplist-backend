<?php
require_once '../lib/dao.php';
require_once '../lib/logger.php';
require_once '../dao/machine.dao.php';

/// This file is responsible for handling machine creation requests.
/// It checks user permissions, decodes the JSON input, and creates a new machine in the database.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    header('Content-Type: application/json; charset=UTF-8');

    $data = json_decode(file_get_contents('php://input'), true);
    $dao = DAO::getInstance();
    $machine = new MACHINE();

    try {
        $machine->fromJson($data);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON structure']);
        exit;
    }

    try {
        if ($dao->create($machine)) {
            http_response_code(201);
            echo json_encode(['message' => 'Machine created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        Logger::safeError('post_machines failed.', array('exception' => $e->getMessage()));
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}
