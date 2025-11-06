<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $r2p2Id = filter_var($body['r2p2_project_id'], FILTER_SANITIZE_NUMBER_INT);
    $user = $module->prepareOnBehalfUser([]);
    $data = $module->getPortal()->requstProjectAccess($r2p2Id, $user);
    if (!empty($data)) {

        echo json_encode(array_merge($data, array('status' => 'success', 'data' => $data)));
    } else {
        echo json_encode(array('status' => 'error', 'data' => $data));
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