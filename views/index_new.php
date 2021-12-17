<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Test Page</title>
    <link href="<?php echo $module->getUrl('dashboard/dist/app.css') . '?t=' . time();; ?>" rel="preload" as="style">
    <link href="<?php echo $module->getUrl('dashboard/dist/app.js') . '?t=' . time();; ?>" rel="preload" as="script">
    <link href="<?php echo $module->getUrl('dashboard/dist/chunk-vendors.css') . '?t=' . time();; ?>" rel="preload"
          as="style">
    <link href="<?php echo $module->getUrl('dashboard/dist/chunk-vendors.js') . '?t=' . time();; ?>" rel="preload"
          as="script">
    <link href="<?php echo $module->getUrl('dashboard/dist/app.css') . '?t=' . time();; ?>" rel="stylesheet">
</head>
<body>
<noscript>
    <strong>We're sorry but <%= htmlWebpackPlugin.options.title %> doesn't work properly without JavaScript enabled.
        Please enable it to continue.</strong>
</noscript>
<app
        pid="<?php echo $module->getProjectId() ?>"
        portal_projects_list='<?php echo json_encode($module->getUser()->getProjectPortalList()) ?>'
        ajaxCreateJiraTicketURL="<?php echo $module->getUrl('ajax/support/create_jira_ticket.php') ?>" ,
        ajaxUserTicketURL="<?php echo $module->getUrl('ajax/user/get_user_tickets.php', false, true) ?>"
        ajaxProjectEMstURL="<?php echo $module->getUrl('ajax/entity/get_project_external_modules.php', false, true) ?>"
        ajaxGenerateSignedAuthURL="<?php echo $module->getUrl('ajax/portal/generate_rma.php', false, true) ?>"
        ajaxAppendSignedAuthURL="<?php echo $module->getUrl('ajax/portal/append_to_existing_ema.php', false, true) ?>"
        ajaxGetSignedAuthURL="<?php echo $module->getUrl('ajax/portal/get_rma.php', false, true) ?>"
        ajaxPortalProjectsListURL="<?php echo $module->getUrl('ajax/user/get_user_r2p2_project_list.php', false, true) ?>"
        attachREDCapURL="<?php echo $module->getURL('ajax/portal/project_attach.php', false, true) . '&pid=' . $module->getProjectId() ?>"
        detachREDCapURL="<?php echo $module->getURL('ajax/portal/project_detach.php', false, true) . '&pid=' . $module->getProjectId() ?>"
        projectPortalSectionURL="<?php echo $module->getURL('ajax/portal/project_setup.php', false, true) . '&pid=' . $module->getProjectId() ?>"
        base_portal_url=<?php echo $module->getClient()->getPortalBaseURL() ?>"
        project_status="<?php echo $module->getProject()->project['status'] ?>"
portal_linkage_header="<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-portal-linkage-tab-header')); ?>
"
tickets_header='<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-ticket-tab-header')); ?>
'
external_modules_header="<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-external-modules-tab-header')); ?>
"
></app>
<!-- built files will be auto injected -->
<script src="<?php echo $module->getUrl('dashboard/dist/chunk-vendors.js') . '?t=' . time();; ?>"></script>
<script src="<?php echo $module->getUrl('dashboard/dist/app.js') . '?t=' . time(); ?>"></script>
</body>
</html>
