<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);


    if (!$body['r2p2_user_id']) {
        throw new \Exception('R2P2 user id is missing');
    }

    $id = filter_var($body['r2p2_user_id'], FILTER_SANITIZE_NUMBER_INT);
    $data = $module->getPortal()->deleteUserFromR2P2Project($id);


    if (!empty($data)) {
        $users = $module->getPortal()->getProjectMembers();
        echo json_encode(array('status' => 'success', 'users' => $users));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>