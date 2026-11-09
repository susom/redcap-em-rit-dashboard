<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

// NOAUTH page: only this module's own cron may run it. It POSTs the shared token (see checkProjectsRMACron()).
if (!$module->isAuthorizedServiceRequest()) {
    http_response_code(403);
    header("Content-type: application/json");
    echo json_encode(array('status' => 'error', 'message' => 'Forbidden'));
    return;
}

// Only production projects get overdue-payment rows, the same projects the daily cron sends.
$project = $module->getProject();
if (!$project || (int)$project->project['status'] !== 1) {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'success', 'message' => 'Skipped pid' . $module->getProjectId() . ': not a production project'));
    return;
}

// The constructor skips this for NOAUTH requests; getHasRMA() below depends on it.
$module->getPortal()->prepareR2P2SavedProject();

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