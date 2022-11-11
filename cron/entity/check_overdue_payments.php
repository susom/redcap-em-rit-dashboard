<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    //if (!isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
    if (!$module->getPortal()->getHasRMA()) {
        $module->getEntity()->checkOverduePayments($module->getProjectId(), $module->getEntity()->getTotalMonthlyPayment($module->getProjectId()));
    }
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