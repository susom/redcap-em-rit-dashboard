<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $data = $module->getPortal()->getR2P2ProjectSOWs();
    foreach ($data as $key => $item){
        $data[$key]['created_at'] = date('m/d/Y H:i:s', strtotime($item['created_at']));
    }
    echo json_encode(array('status' => 'success', 'sprint_blocks' => $data));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>