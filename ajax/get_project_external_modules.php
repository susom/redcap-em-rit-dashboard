<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $result = array();
    $pointer = 0;
    foreach ($module->getEntity()->getProjectEmUsageRecords($module->getProjectId()) as $entity) {
        $data = $entity->getData();
        $row = array(
            $data['module_prefix'],
            $data['is_em_enabled'] == true ? 'Yes' : 'No',
            $data['maintenance_fees'] != '' ? $data['maintenance_fees'] : 0,
        );
        $pointer++;
        $result['data'][] = $row;
    }
    $result['draw'] = $pointer;
    $result['recordsTotal'] = $pointer;
    $result['recordsFiltered'] = $pointer;
    $result['pageActive'] = 1;

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