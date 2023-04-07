<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $rmaId = filter_var($_GET['rma_id'], FILTER_SANITIZE_NUMBER_INT);
    $data = $module->getPortal()->getRMALineItems($module->getPortal()->projectPortalSavedConfig['portal_project_id'], $rmaId, USERID);
    if (!empty($data)) {
        echo json_encode(array('status' => 'success', 'data' => $data));
    } else {
        echo json_encode(array('status' => 'success'));
    }
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>