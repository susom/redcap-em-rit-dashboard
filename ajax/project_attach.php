<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $portalProjectId = filter_var($_POST['project_portal_id'], FILTER_SANITIZE_NUMBER_INT);
    $module->attachToProjectPortal($portalProjectId);
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}
?>