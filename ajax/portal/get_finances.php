<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $data = $module->getPortal()->getProjectFinancesRecords();
    if (!empty($data)) {
        echo json_encode(array_merge($data, array('status' => 'success', 'data' => $data)));
    } else {
        echo json_encode(array('status' => 'success', 'data' => []));
    }
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>