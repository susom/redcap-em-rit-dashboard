<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $result = array();
    $pointer = 0;
    foreach ($module->getUser()->getUserJiraTickets() as $jiraTicket) {
        $row = array(
            'id' => "<a target='_blank' href='" . $module->getClient()->getPortalBaseURL() . "support?id=" . $jiraTicket['id'] . "'>" . $jiraTicket['jira']['key'] . "</a>",
            'title' => $jiraTicket['jira']['fields']['summary'],
            //'type' => $jiraTicket['jira']['fields']['issuetype']['name'],
            'status' => $jiraTicket['jira']['fields']['status']['name'],
            'status_category' => $jiraTicket['jira']['fields']['status']['statusCategory']['name'],
            'created_at' => date('m/d/Y H:i:s', strtotime($jiraTicket['created_at'])),
            'modified_at' => date('m/d/Y  H:i:s', strtotime($jiraTicket['jira']['fields']['updated'])),
            'redcap_pid' => $jiraTicket['redcap_pid'],
            'current_pid' => $jiraTicket['redcap_pid'] === (int)$module->getProjectId()
        );
        $pointer++;
        $result['data'][] = $row;
    }
    $result['draw'] = $pointer;
    $result['recordsTotal'] = $pointer;
    header('Content-Type: application/json');
    echo json_encode($result);
} catch (\LogicException | ClientException | GuzzleException $e) {
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