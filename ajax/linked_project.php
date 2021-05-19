<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $result = array();

    foreach ($module->getUser()->getProjectPortalList() as $project) {
        if ($project['project_deleted_at']) {
            continue;
        }

        if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) && $module->getPortal()->projectPortalSavedConfig['portal_project_id'] == $project['id']) {

            $project['status'] = 'success';
            $result = $project;
        }
    }
    header('Content-Type: application/json');
    if (!empty($result)) {
        echo json_encode($result);
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'no linked project found'));
    }

} catch (\LogicException $e) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>