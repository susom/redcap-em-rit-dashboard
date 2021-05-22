<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $result = array();
    foreach ($module->getUser()->getUserJiraTickets() as $jiraTicket) {
        $row = array(
            $jiraTicket['id'],
            $jiraTicket['jira']['fields']['summary'],
            $jiraTicket['jira']['fields']['description'],
            $jiraTicket['jira']['fields']['issuetype']['name'],
            $jiraTicket['jira']['key'],
            $jiraTicket['jira']['fields']['status']['name'],
            date('m/d/Y', strtotime($jiraTicket['created_at']))
        );
        $result['data'][] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($result);
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