<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $result = array();
    $pointer = 0;
    if ($module->getUser()->getProjectPortalList()) {
        foreach ($module->getUser()->getProjectPortalList() as $project) {
            if ($project['project_deleted_at']) {
                continue;
            }
            $row = array(
                'name' => $project['project_name'],
                'description' => $project['project_description'],
                'id' => $project['id'],
                'url' => '',
                'linked' => isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) && $module->projectPortalSavedConfig['portal_project_id'] == $project['id']
            );
            $pointer++;
            $result['data'][] = $row;
        }
    }

    $extra = array(
        'name' => 'Creat New Research Portal Project',
        'description' => '',
        'id' => '',
        'url' => $module->getClient()->getPortalBaseURL() . 'create',
        'linked' => false
    );
    $result['data'][] = $extra;
    $result['draw'] = $pointer;
    $result['recordsTotal'] = $pointer;

    header('Content-Type: application/json');
    echo json_encode($result);
} catch (\LogicException | ClientException | GuzzleException $e) {
    header("Content-type: application/json");
//    http_response_code(404);
    $result['data'] = [];
    echo json_encode($result);
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>