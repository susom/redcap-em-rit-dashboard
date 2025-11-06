<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $portalProjectId = filter_var($body['project_portal_id'], FILTER_SANITIZE_NUMBER_INT);
    $portalProjectName = filter_var($body['project_portal_name'], FILTER_SANITIZE_STRING);
    $portalProjectDescription = filter_var($body['project_portal_description'], FILTER_SANITIZE_STRING);
    $inputs = $module->getPortal()->attachToProjectPortal($portalProjectId, $portalProjectName, $portalProjectDescription);
    //$module->savePortalProjectInfoInREDCap($inputs);
    echo json_encode(array('status' => 'success', 'message' => $module->getNotifications()['attach_redcap_to_r2p2_success_message'] . $portalProjectName, 'portal_project' => $inputs));
} catch (\LogicException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
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