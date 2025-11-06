<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $term = htmlentities($body['term']);
    $data = $module->getPortal()->searchUsers($term);
    if (!empty($data)) {
        $users = [];
        foreach ($data['hits']['hits'] as $item) {
            $users[] = $item['_source'];
        }
        echo json_encode(array_merge(array('status' => 'success', 'users' => $users)));
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