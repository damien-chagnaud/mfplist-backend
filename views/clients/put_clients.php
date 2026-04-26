<?php
require_once '../lib/dao.php';
require_once '../lib/clients.php';

/// This file is responsible for handling client update requests.
/// It checks user permissions, processes the incoming JSON data, and updates the client in the database.
if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    http_content_type('application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $dao = DAO::getInstance();
    $client = new CLIENTS();

    try {
        $client->fromJson($data);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON structure']);
        exit;
    }

    try {
        if ($dao->updateByUUID($client)) {
            http_response_code(200);
            echo json_encode(['message' => 'Client updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log('put_clients update failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }

} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}