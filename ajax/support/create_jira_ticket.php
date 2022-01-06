<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $portalProjectId = $body['project_portal_id'];
    $redcapProjectId = $body['redcap_project_id'];;
    if (!isset($body['redcap-project-id'])) {
        $redcapProjectId = $module->getProjectId();
    }
    if (!isset($body['summary'])) {
        throw new \Exception("summary is required");
    }

    if (!isset($body['type'])) {
        throw new \Exception("Issue Type is required");
    }
    if (!isset($body['description'])) {
        throw new \Exception("description is required");
    }
    $summary = '[REDCap] ' . filter_var($body['summary'], FILTER_SANITIZE_STRING);
//    $issueType = filter_var($body['type'], FILTER_SANITIZE_NUMBER_INT);
    $types = $module->getSupport()->getJiraIssueTypes();
    $issueType = array_key_first($types);
    $description = filter_var($body['description'], FILTER_SANITIZE_STRING);

    $data = $module->getSupport()->createJiraTicketViaPortal($redcapProjectId, $summary, $issueType, $description, $portalProjectId, $module->getProject()->project['app_title'], $user_firstname, $user_lastname);
    echo json_encode(array_merge($data, array('status' => 'success', 'message' => "<a target='_blank' href='" . $module->getClient()->getPortalBaseURL() . "support?id=" . $data['id'] . "'><h3>" . $data['jira']['key'] . "</h3></a>&nbsp;<h3>been created</h3>")));
    echo json_encode(array('message' => 'jira ticket created '));
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