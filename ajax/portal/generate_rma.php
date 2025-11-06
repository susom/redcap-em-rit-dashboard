<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $portalProjectId = $body['project_portal_id'];
    $redcapProjectId = $body['redcap_project_id'];;
    $monthlyFees = $body['monthly_fees'];;
    if (!isset($body['redcap_project_id'])) {
        $redcapProjectId = $module->getProjectId();
    }
    if (!isset($body['external_modules'])) {
        throw new \Exception("external_modules is required");
    }

    if (!isset($body['project_portal_id']) || !isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) || $module->getPortal()->projectPortalSavedConfig['portal_project_id'] != $body['project_portal_id']) {
        throw new \Exception("portal project id is required");
    }

    # before generating RMA lets make sure to update EM list to capture correct price.
    $module->getManagerEm()->updateProjectEMUtil($module->getProjectId());

    #$ems = $body['external_modules'];
    $ems = $module->getEntity()->generateProjectEMUsageArray($module->getProjectId());
    // before generating RMA check if overdue payment exists
    $overdue = $module->processOverduePayments();

    $external_modules = json_encode($ems);


    $data = $module->getPortal()->generateREDCapSignedAuthInPortal($portalProjectId, $redcapProjectId, $external_modules, USERID, $overdue);
    $data['sow_status'] = $data['status'];
    if ($overdue) {
        $module->getEntity()->updateProcessedOverduePayments($module->getProjectId());
    }

    $module->setState($module->getProject()->project['status'] == '1', $monthlyFees, true, true, $data['sow_status']);

    echo json_encode(array_merge($data, array('state' => $module->getState(), 'status' => 'success', 'message' => $module->getNotifications()['generate_rma_success_message'], 'ems' => $ems, 'link' => $module->getClient()->getPortalBaseURL() . 'detail/' . $module->getPortal()->projectPortalSavedConfig['portal_project_id'] . '/sow/' . $data['id'])));
} catch (ClientException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();
    $message = json_decode($responseBodyAsString, true);
    echo json_encode(array('status' => 'error', 'message' => $message['message']));
}
catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>