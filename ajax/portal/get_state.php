<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {

    $data = $module->getState();
    if (!empty($data)) {
        $fiances = $module->getPortal()->getProjectFinancesRecords();
        echo json_encode(array('status' => 'success', 'state' => $data));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>