<?php
include '../lib/dao.php';
include '../dao/parts.dao.php';
include '../dao/timestamp.dao.php';

include 'head.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['USER_LEVEL'] > 0) {
    
    $result = [];
    $dao = DAO::getInstance();
    $results = $dao->read(new PARTS(),false,true);

    if (!$results) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed']);
        exit;
    }
    if (count($results) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'No parts found']);
        exit;
    }
    echo json_encode($results);

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['USER_LEVEL'] > 0) {
    // The request is using the POST method
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }

    if (!isset($data['action'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    switch ($data['action']) {
        case 'synctime':
            try{
                $timestamp = new TIMESTAMP();
                $timestamp->setName(PARTS::getTableName());
                $result = DAO::getInstance()->read($timestamp, false, true);

                if (!$result) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to read timestamp']);
                    exit;
                }
                $refresh = $result[0]['refresh'] ?? null;
                $refreshTime = strtotime($refresh);
                echo "{\"synctime\":\"$refreshTime\"}";

            }catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to get refresh timestamp']);
                exit;
            }
            
            break;
       /* case 'add':
            if (!isset($data['name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing name for new part']);
                exit;
            }
            $part = new PARTS();
            $part->setName($data['name']);
            $part->setRefresh($data['refresh']);
            $result = DAO::getInstance()->create($part);
            break;

        case 'update':
            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing ID for update']);
                exit;
            }
            $part = new PARTS();
            $part->setId($data['id']);
            $part->setName($data['name'] ?? null);
            $part->setRefresh($data['refresh'] ?? null);
            $result = DAO::getInstance()->update($part);
            break;

        case 'delete':
            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing ID for deletion']);
                exit;
            }
            $result = DAO::getInstance()->delete(new PARTS(), ['id' => $data['id']]);
            break;*/

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            exit;
    }

    $dao = DAO::getInstance();


    
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['USER_LEVEL'] > 1) {
    // The request is using the PUT method
}








    
?>



