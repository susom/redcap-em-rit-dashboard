<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $pta['pta_charge_number'] = $body['pta_number'];
    $pta = $module->prepareOnBehalfUser($pta);


    if (!$pta['pta_charge_number']) {
        throw new \Exception('PTA number is missing');
    }

    $data = $module->getPortal()->createNewPTA($pta);
    if (!empty($data)) {
        $fiances = $module->getPortal()->getProjectFinancesRecords();
        echo json_encode(array_merge($data, array('status' => 'success', 'finances' => $fiances)));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>