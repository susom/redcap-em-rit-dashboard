<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

// NOAUTH page for the REDCap Notifications API module, which POSTs its rule's "Request body" (JSON). That body must
// carry "secret_token" equal to this module's notifications-api-token setting. The filters stay in the URL.
if (!$module->isAuthorizedServiceRequest('notifications-api-token')) {
    http_response_code(403);
    header("Content-type: application/json");
    echo json_encode(array('status' => 'error', 'message' => 'Forbidden'));
    return;
}

try{
    $list = $module->getNotificationProjects();
    header("Content-type: application/json");
    echo json_encode($list);

}catch (\Exception $e){
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
