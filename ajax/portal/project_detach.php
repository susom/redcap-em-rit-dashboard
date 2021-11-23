<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $portalProjectId = filter_var($body['project_portal_id'], FILTER_SANITIZE_NUMBER_INT);
    $redcapProjectId = filter_var($body['redcap_project_id'], FILTER_SANITIZE_STRING);
    $inputs = $module->getPortal()->detachPortalProject($portalProjectId, $redcapProjectId);
    $module->savePortalProjectInfoInREDCap($inputs);
} catch (\LogicException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>