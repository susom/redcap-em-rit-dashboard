<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $portalProjectId = filter_var($_POST['portal-project-id'], FILTER_SANITIZE_NUMBER_INT);
    $redcapProjectId = filter_var($_POST['redcap-project-id'], FILTER_SANITIZE_NUMBER_INT);
    if (!isset($_POST['redcap-project-id'])) {
        $redcapProjectId = $module->getProjectId();
    }
    if (!isset($_POST['summary'])) {
        throw new \Exception("summary is required");
    }
    if (!isset($_POST['summary'])) {
        throw new \Exception("summary is required");
    }
    if (!isset($_POST['issue-types-id'])) {
        throw new \Exception("Issue Type is required");
    }
    if (!isset($_POST['description'])) {
        throw new \Exception("description is required");
    }
    $summary = filter_var($_POST['summary'], FILTER_SANITIZE_STRING);
    $issueType = filter_var($_POST['issue-types-id'], FILTER_SANITIZE_NUMBER_INT);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

    $data = $module->getSupport()->createJiraTicketViaPortal($redcapProjectId, $summary, $issueType, $description, $portalProjectId);
    echo json_encode(array_merge($data, array('status' => 'success')));
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