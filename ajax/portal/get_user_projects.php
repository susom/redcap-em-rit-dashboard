<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $suid = htmlentities($body['suid']);
    $data = $module->getPortal()->getR2P2UserProjects($suid);
    if (!empty($data)) {
        echo json_encode(array_merge(array('status' => 'success', 'projects' => $data)));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>