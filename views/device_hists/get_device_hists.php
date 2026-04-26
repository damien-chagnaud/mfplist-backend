<?php
// route: views/device_hists/get_device_hists.php

if ($_SERVER['SECURED'] && $_SERVER['USER_LEVEL'] > 1) {
    http_content_type('application/json');

    $dao = DAO::getInstance();
    $deviceHist = new DEVICE_HISTS();

    try {
        $deviceHists = $dao->read($deviceHist, false, true);
        http_response_code(200);
        echo json_encode($deviceHists);
    } catch (Exception $e) {
        http_response_code(500);
        error_log('get_device_hists failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }

} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Insufficient permissions']);
    exit;
}