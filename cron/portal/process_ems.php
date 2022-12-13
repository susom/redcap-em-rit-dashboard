<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    // this will replicate em_charges and create charges for rma
    $module->getPortal()->replicateREDCapEMCharges();

    // get all charges from all instances
    $charges = $module->getManagerEm()->getManagerEMObject()->processCustomCharges();
    // Test

    // Push custom charges into R2P2
    $module->getPortal()->pushChargesToR2P2($charges);
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'success', 'message' => 'Cron Processed for pid' . $module->getProjectId()));
} catch (\LogicException|ClientException|GuzzleException $e) {
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