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
        ajaxCreateJiraTicketURL="<?php echo $module->getUrl('ajax/create_jira_ticket.php') ?>" ,
        ajaxUserTicketURL="<?php echo $module->getUrl('ajax/get_user_tickets.php', false, true) ?>"
        ajaxProjectEMstURL="<?php echo $module->getUrl('ajax/get_project_external_modules.php', false, true) ?>"
        ajaxGenerateSignedAuthURL="<?php echo $module->getUrl('ajax/generate_signed_auth.php', false, true) ?>"
        ajaxAppendSignedAuthURL="<?php echo $module->getUrl('ajax/append_approved_signed_auth.php', false, true) ?>"
        ajaxGetSignedAuthURL="<?php echo $module->getUrl('ajax/get_signed_auth.php', false, true) ?>"
        ajaxPortalProjectsListURL="<?php echo $module->getUrl('ajax/portal_project_list.php', false, true) ?>"
        attachREDCapURL="<?php echo $module->getURL('ajax/project_attach.php', false, true) . '&pid=' . $module->getProjectId() ?>"
        detachREDCapURL="<?php echo $module->getURL('ajax/project_detach.php', false, true) . '&pid=' . $module->getProjectId() ?>"
        projectPortalSectionURL="<?php echo $module->getURL('ajax/project_setup.php', false, true) . '&pid=' . $module->getProjectId() ?>"
></app>
<!-- built files will be auto injected -->
<script src="<?php echo $module->getUrl('dashboard/dist/chunk-vendors.js') . '?t=' . time();; ?>"></script>
<script src="<?php echo $module->getUrl('dashboard/dist/app.js') . '?t=' . time(); ?>"></script>
</body>
</html>
