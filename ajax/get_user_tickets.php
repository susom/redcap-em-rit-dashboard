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
            "<a target='_blank' href='https://rit-portal.med.stanford.edu/support?id=" . $jiraTicket['id'] . "'>" . $jiraTicket['id'] . "</a>",
            "<a target='_blank' href='https://rit-portal.med.stanford.edu/support?id=" . $jiraTicket['id'] . "'>" . $jiraTicket['jira']['fields']['summary'] . "</a>",
            $jiraTicket['jira']['fields']['description'],
            $jiraTicket['jira']['fields']['issuetype']['name'],
            "<a target='_blank' href='https://stanfordmed.atlassian.net/browse/" . $jiraTicket['jira']['key'] . "'>" . $jiraTicket['jira']['key'] . "</a>",
            $jiraTicket['jira']['fields']['status']['name'],
            date('m/d/Y', strtotime($jiraTicket['created_at']))
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