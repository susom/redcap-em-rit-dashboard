<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $result = $module->getManagerEm()->updateProjectEMUtil($module->getProjectId());
    if ($result) {
        echo json_encode(array('status' => 'success', 'message' => $module->getNotifications()['update_em_list_success']));
    } else {
        throw new \Exception("could not update project EMs. Please contact REDCap admin");
    }

} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>