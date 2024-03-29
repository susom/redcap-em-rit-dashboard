<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
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

    $raise_on_behalf_of = USERID;
    $firstName = $user_firstname;
    $lastName = $user_lastname;
    if (isset($body['on_behalf_of']) && $body['on_behalf_of'] != '') {
        if (defined('SUPER_USER') && SUPER_USER == 1) {
            $raise_on_behalf_of = filter_var($body['on_behalf_of'], FILTER_SANITIZE_STRING);
            $user = ExternalModules::getUserInfo($raise_on_behalf_of);
            $firstName = $user['user_firstname'];
            $lastName = $user['user_lastname'];
        }
    }

    $summary = '[REDCap] ' . filter_var($body['summary'], FILTER_SANITIZE_STRING);
//    $issueType = filter_var($body['type'], FILTER_SANITIZE_NUMBER_INT);
    $types = $module->getSupport()->getJiraIssueTypes();
    $issueType = array_key_first($types);
    $description = filter_var($body['description'], FILTER_SANITIZE_STRING);

    $data = $module->getSupport()->createJiraTicketViaPortal($redcapProjectId, $summary, $issueType, $description, $portalProjectId, $module->getProject()->project['app_title'], $raise_on_behalf_of, $firstName, $lastName);
    echo json_encode(array_merge($data, array('status' => 'success', 'message' => "<a target='_blank' href='" . $module->getClient()->getPortalBaseURL() . "support?id=" . $data['id'] . "'><h3>" . $data['jira']['key'] . "</h3></a>&nbsp;<h3>has been created</h3>")));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>