<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $module->getPortal()->prepareR2P2SavedProject();
    $portalProjectId = $module->getPortal()->projectPortalSavedConfig['portal_project_id'];
    $description = $body['description'];
    $redcap_admin = $body['redcap_admin'];
    $sprintBlockId = $body['id']['id'];
    $fundingSource = $body['fundingSource']['id'];

    if (!$description) {
        throw new \Exception("Sprint block description is missing");
    }

    if (!$sprintBlockId) {
        throw new \Exception("Please select sprint block size. ");
    }
    $sprintBlock = $module->getPortal()->searchServiceBlock($sprintBlockId, $fundingSource);

    // if user provided info about meeting with redcap admin please attach it to the description.
    if ($redcap_admin) {
        $description .= '<br><strong>Have you met with someone from REDCap team? If so, please describe.</strong><br>' . $redcap_admin;
    }


    $work_items = array(array('total_amount' => $sprintBlock['price'], 'description' => $description, 'text' => $sprintBlock['text']));
    $data = $module->getPortal()->generateR2P2SOW($portalProjectId, $module->getProjectId(), $work_items);

    echo json_encode(array_merge($data, array('status' => 'success', 'message' => 'New Service Block Statement of Work was created in R2P2. Please click this &nbsp;<a target="_blank" href="' . $module->getPortal()->projectPortalSavedConfig['portal_project_url'] . '/sow?id=' . $data['id'] . '"><strong>link</strong></a>&nbsp;')));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>