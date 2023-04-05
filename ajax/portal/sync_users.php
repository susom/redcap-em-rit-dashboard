<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $user['group_id'] = $body['group_id'];
    $user['username'] = $body['redcap_username'];


    if (!$user['username']) {
        throw new \Exception('Username is missing');
    }
    if (!$user['group_id']) {
        throw new \Exception('Group Id is missing');
    }

    if (!$body['r2p2_user_id'] or $body['r2p2_user_id'] == '') {
        $object = ExternalModules::getUserInfo($user['username']);
        $user['email'] = $object['user_email'];
        $user['first_name'] = $object['user_firstname'];
        $user['last_name'] = $object['user_lastname'];
        $data = $module->getPortal()->addUserToR2P2Project($user);
    } else {
        $id = filter_var($body['r2p2_user_id'], FILTER_SANITIZE_NUMBER_INT);
        $data = $module->getPortal()->updateUserToR2P2Project($user, $id);
    }


    if (!empty($data)) {
        $users = $module->getPortal()->getProjectMembers();
        echo json_encode(array('status' => 'success', 'users' => $users));
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