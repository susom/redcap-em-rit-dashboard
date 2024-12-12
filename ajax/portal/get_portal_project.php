<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */
try {
    $data = [
        'redcap_project_id' => $module->getProjectId(),
        'summary' => "",
        'type' => "",
        'description' => "",
        'on_behalf_of' => "",
        'project_portal_type' => '',
        'project_portal_description' => '',
        'project_portal_id'               => isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) ? $module->getPortal()->projectPortalSavedConfig['portal_project_id'] : '',
        'project_portal_name'             => isset($module->getPortal()->projectPortalSavedConfig['project_portal_name']) ? $module->getPortal()->projectPortalSavedConfig['project_portal_name'] : '',
        'project_portal_id_saved'         => isset($module->getPortal()->projectPortalSavedConfig['project_portal_id_saved']) ? $module->getPortal()->projectPortalSavedConfig['project_portal_id_saved'] : '',
        'project_portal_url'              => isset($module->getPortal()->projectPortalSavedConfig['project_portal_url']) ? $module->getPortal()->projectPortalSavedConfig['project_portal_url'] : '',
        'project_portal_sow_url'          => isset($module->getPortal()->projectPortalSavedConfig['project_portal_sow_url']) ? $module->getPortal()->projectPortalSavedConfig['project_portal_sow_url'] : '',
        'project_portal_consultation_url' => isset($module->getPortal()->projectPortalSavedConfig['project_portal_consultation_url']) ? $module->getPortal()->projectPortalSavedConfig['project_portal_consultation_url'] : '',
    ];
    echo json_encode(array('status' => 'success', 'data' => $data));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}