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
    $sprintBlockId = $body['id']['id'];

    if (!$description) {
        throw new \Exception("Sprint block description is missing");
    }

    if (!$sprintBlockId) {
        throw new \Exception("Please select sprint block size. ");
    }
    $sprintBlock = $module->getPortal()->searchServiceBlock($sprintBlockId);
    $sprintBlockType = $module->getPortal()->searchWorkItemType('sprint_block');

    if (empty($sprintBlockType)) {
        throw new \Exception("Could not find Service block type.");
    }
    $work_items = array(array('type_id' => $sprintBlockType['id'], 'total_amount' => $sprintBlock['price'], 'description' => $description, 'number_of_months' => 1));
    $data = $module->getPortal()->generateR2P2SOW($portalProjectId, $module->getProjectId(), $work_items);

    echo json_encode(array_merge($data, array('status' => 'success', 'message' => 'New Service Block Statement of Work was created in R2P2. Please click this &nbsp;<a target="_blank" href="' . $module->getPortal()->projectPortalSavedConfig['portal_project_url'] . '/sow/' . $data['id'] . '"><strong>link</strong></a>&nbsp; and Approve the Statement of Work.')));
} catch (\LogicException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (ClientException $e) {
    // for regular request if failed try to generate new token and try again. otherwise throw exception.
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (GuzzleException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>