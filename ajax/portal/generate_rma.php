<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $portalProjectId = $body['project_portal_id'];
    $redcapProjectId = $body['redcap_project_id'];;
    if (!isset($body['redcap_project_id'])) {
        $redcapProjectId = $module->getProjectId();
    }
    if (!isset($body['external_modules'])) {
        throw new \Exception("external_modules is required");
    }

    if (!isset($body['project_portal_id']) || !isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) || $module->getPortal()->projectPortalSavedConfig['portal_project_id'] != $body['project_portal_id']) {
        throw new \Exception("portal project id is required");
    }

    $ems = $body['external_modules'];
    // before generating RMA check if overdue payment exists
    $overdue = $module->getEntity()->getOverduePayments($module->getProjectId());
    if (!empty($overdue)) {
        $month = date('m', time());
        foreach ($overdue as $item) {
            // no need to add current month overdue payment.
            if ($month == $item['month']) {
                continue;
            }
            $ems[] = array(
                'prefix' => 'Overdue payment for month of ' . date("F", strtotime('00-' . $month . '-01')),
                'maintenance_fees' => $item['monthly_payments']
            );
        }
    }

    $external_modules = json_encode($ems);

    $data = $module->getPortal()->generateREDCapSignedAuthInPortal($portalProjectId, $redcapProjectId, $external_modules, USERID);
    $data['sow_status'] = $data['status'];
    if ($overdue) {
        $module->getEntity()->deleteOverduePayments($module->getProjectId());
    }
    echo json_encode(array_merge($data, array('status' => 'success', 'message' => $module->getNotifications()['generate_rma_success_message'], 'link' => $module->getClient()->getPortalBaseURL() . 'detail/' . $module->getPortal()->projectPortalSavedConfig['portal_project_id'] . '/sow/' . $data['id'])));
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