<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */


try {


    ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <style>
        #user-tickets_processing {
            margin-top: 5% !important;
        }
    </style>
    <div class="container-fluid">

        <?php
        echo $module->getSystemSetting('rit-dashboard-main-header');
        ?>
        <div class="row">
            <div class="col-sm pt-4">
                <ul class="nav nav-tabs" id="tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="portal-linkage-tab" data-toggle="tab" href="#portal-linkage"
                           role="tab"
                           aria-controls="portal-linkage" aria-selected="true">Portal Linkage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " id="tickets-tab" data-toggle="tab" href="#tickets" role="tab"
                           aria-controls="tickets" aria-selected="true">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="external-modules-tab" data-toggle="tab" href="#external-modules"
                           role="tab"
                           aria-controls="external-modules" aria-selected="true">External Modules</a>
                    </li>
                    <!--                    <li class="nav-item">-->
                    <!--                        <a class="nav-link " id="services-tab" data-toggle="tab" href="#services" role="tab"-->
                    <!--                           aria-controls="services" aria-selected="false">Services</a>-->
                    <!--                    </li>-->
                    <!--                    <li class="nav-item">-->
                    <!--                        <a class="nav-link" id="snapshot-tab" data-toggle="tab" href="#snapshot" role="tab"-->
                    <!--                           aria-controls="snapshot" aria-selected="false">Current Snapshot</a>-->
                    <!--                    </li>-->
                    <!--                    <li class="nav-item">-->
                    <!--                        <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab"-->
                    <!--                           aria-controls="history" aria-selected="false">History</a>-->
                    <!--                    </li>-->
                </ul>

                <div class="tab-content pt-4" id="myTabContent">
                    <div class="tab-pane fade   show active" id="portal-linkage" role="tabpanel"
                         aria-labelledby="portal-linkage-tab">
                        <?php
                        require_once("tabs/portal_linkage.php");
                        ?>
                    </div>
                    <div class="tab-pane fade " id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                        <?php
                        require_once("tabs/tickets.php");
                        ?>
                    </div>
                    <div class="tab-pane fade" id="external-modules" role="tabpanel"
                         aria-labelledby="external-modules-tab">
                        <?php
                        require_once("tabs/external_modules.php");
                        ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <?php
    require_once("modal.php");
    ?>
    <script src="<?php echo $module->getUrl('assets/js/index.js') ?>"></script>
    <script src="<?php echo $module->getUrl('assets/js/project_setup.js') ?>"></script>
    <script>
        Main.ajaxCreateJiraTicketURL = "<?php echo $module->getUrl('ajax/create_jira_ticket.php') ?>"
        Main.ajaxUserTicketURL = "<?php echo $module->getUrl('ajax/get_user_tickets.php') ?>"
        Main.ajaxProjectEMstURL = "<?php echo $module->getUrl('ajax/get_project_external_modules.php') ?>"
        Main.ajaxPortalProjectsListURL = "<?php echo $module->getUrl('ajax/portal_project_list.php') ?>"
        Main.init()
    </script>
    <?php
} catch (\Exception $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}