<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $irbNum = filter_var($body['irb_num'], FILTER_SANITIZE_NUMBER_INT);
    $data = $module->getPortal()->searchIRB($irbNum);
    if (!empty($data)) {

        echo json_encode(array_merge($data, array('status' => 'success', 'data' => $data)));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>