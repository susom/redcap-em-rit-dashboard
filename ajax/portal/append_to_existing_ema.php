<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $portalProjectId = $body['project_portal_id'];
    $redcapProjectId = $body['redcap_project_id'];;
    $portalSowId = $body['portal_sow_id'];;
    $monthlyFees = $body['monthly_fees'];;
    if (!isset($body['redcap_project_id'])) {
        $redcapProjectId = $module->getProjectId();
    }
    if (!isset($body['portal_sow_id'])) {
        throw new \Exception("portal_sow_id is required");
    }

    if (!isset($body['external_modules'])) {
        throw new \Exception("external_modules is required");
    }

    $external_modules = json_encode($body['external_modules']);

    if (!isset($body['project_portal_id']) || !isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) || $module->getPortal()->projectPortalSavedConfig['portal_project_id'] != $body['project_portal_id']) {
        throw new \Exception("portal project id is required");
    }

    // before generating RMA check if overdue payment exists
    $overdue = $module->processOverduePayments();
    $module->setState($module->getProject()->project['status'] == '1', $monthlyFees, true, true, $data['sow_status']);

    $data = $module->getPortal()->appendApprovedREDCapSignedAuthInPortal($portalProjectId, $redcapProjectId, $portalSowId, $external_modules, USERID, $overdue);
    $data['sow_status'] = $data['status'];
    if ($overdue) {
        $module->getEntity()->updateProcessedOverduePayments($module->getProjectId());
    }
    echo json_encode(array_merge($data, array('state' => $module->getState(), 'status' => 'success', 'message' => $module->getNotifications()['append_rma_success_message'], 'link' => $module->getClient()->getPortalBaseURL() . 'detail/' . $module->getPortal()->projectPortalSavedConfig['portal_project_id'] . '/sow/' . $data['id'])));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>