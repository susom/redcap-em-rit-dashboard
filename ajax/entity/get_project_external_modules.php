<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {

    $result['data'] = $module->getEntity()->generateProjectEMUsageArray($module->getProjectId());
    $result['draw'] = count($result['data']);
    $result['recordsTotal'] = count($result['data']);
    $result['recordsFiltered'] = count($result['data']);
    $result['pageActive'] = 1;
    header('Content-Type: application/json');
    echo json_encode($result);
} catch (\LogicException | ClientException | GuzzleException $e) {
    header("Content-type: application/json");
//    http_response_code(404);
    $result['data'] = [];
    echo json_encode($result);
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>