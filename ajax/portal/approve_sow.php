<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    if ($body['pta_number'] != '') {
        $approval['pta'] = htmlentities($body['pta_number']);
    }
    if ($body['registered_pta'] != '') {
        $approval['registered_pta'] = htmlentities($body['registered_pta']);
    }

    // only check for PTA if project incurring monthly fees.
    $monthlyFees = $module->getEntity()->getTotalMonthlyPayment($module->getProjectId());
    if ($monthlyFees > 0 && !$approval['pta_number'] && !$approval['registered_pta']) {
        throw new \Exception('PTA is missing. Please select or create a new one.');
    }

    $approval['signature'] = htmlentities($body['reviewer_name']) ?: '';
    $approval['comment'] = htmlentities($body['comment']) ?: '';
    $approval = $module->prepareOnBehalfUser($approval);
    $sowId = htmlentities($body['sow_id']);

    if (!$approval['signature']) {
        throw new \Exception('Reviewer Name is missing');
    }


    $data = $module->getPortal()->approveSOW($approval, $sowId);
    if (!empty($data)) {
        // add current user to newly created project.
        $user = $module->framework->getUser();
        $username = $user->getUsername();
        $user = ExternalModules::getUserInfo($username);
        $module->getPortal()->addProjectUser($data['id'], $user);

        echo json_encode(array_merge($data, array('status' => 'success', 'project_portal_id' => $data['id'])));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
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