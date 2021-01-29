<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $portalProjectId = filter_var($_POST['project_portal_id'], FILTER_SANITIZE_NUMBER_INT);
    $portalProjectName = filter_var($_POST['project_portal_name'], FILTER_SANITIZE_STRING);
    $portalProjectDescription = filter_var($_POST['project_portal_description'], FILTER_SANITIZE_STRING);
    $module->attachToProjectPortal($portalProjectId, $portalProjectName, $portalProjectDescription);
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}
?>