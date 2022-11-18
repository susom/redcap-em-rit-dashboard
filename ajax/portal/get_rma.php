<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $data = $module->getPortal()->getREDCapSignedAuthInPortal($module->getPortal()->projectPortalSavedConfig['portal_project_id'], $module->getProjectId());
    if (!empty($data)) {
        $data['sow_status'] = $data['status'];
        $monthlyFees = $module->getEntity()->getTotalMonthlyPayment($module->getProjectId());
        $module->setState($module->getProject()->project['status'] == '1', $monthlyFees, isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']), $module->getPortal()->getHasRMA(), $module->getPortal()->getRMAStatus());
        $data['state'] = $module->getState();
        echo json_encode(array_merge($data, array('status' => 'success', 'link' => $module->getClient()->getPortalBaseURL() . 'detail/' . $module->getPortal()->projectPortalSavedConfig['portal_project_id'] . '/sow?id=' . $data['id'])));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
} catch (\LogicException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (ClientException $e) {
    // for regular request if failed try to generate new token and try again. otherwise throw exception.
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (GuzzleException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>