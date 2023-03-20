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

    $approval['signature'] = htmlentities($body['reviewer_name']) ?: '';
    $approval['comment'] = htmlentities($body['comment']) ?: '';
    $user = $module->framework->getUser();
    $username = $user->getUsername();
    $user = ExternalModules::getUserInfo($username);
    $approval['on_behalf_of_email'] = $user['user_email'];
    $approval['on_behalf_of_username'] = $user['username'];
    $approval['on_behalf_of_first_name'] = $user['user_firstname'];
    $approval['on_behalf_of_last_name'] = $user['user_lastname'];
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