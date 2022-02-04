<?php

namespace Stanford\ProjectPortal;

use Dompdf\Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    if (!isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
        throw new \LogicException("This REDCap project is not linked to R2P2 project.");
    }
    $data = $module->getPortal()->updateRMA($module->getPortal()->projectPortalSavedConfig['portal_project_id']);
    echo json_encode(array('status' => 'success', 'message' => $data['message']));
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