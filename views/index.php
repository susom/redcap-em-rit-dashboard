<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */


try {


    ?>
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
                        <a class="nav-link active" id="history-tab" data-toggle="tab" href="#tickets" role="tab"
                           aria-controls="tickets" aria-selected="true">Tickets</a>
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
                    <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                        <div class="row">
                            <div class="col-sm">
                                <div class="card">
                                    <!--            <img src="..." class="card-img-top" alt="...">-->
                                    <div class="card-body">
                                        <h5 class="card-title">Basic Services</h5>
                                        <p class="card-text">REDCap is available for research use to all Stanford
                                            students,
                                            staff, and
                                            faculty at no cost. The free offering includes:
                                        <ul>
                                            <li><b>Project Hosting:</b> REDCap will be securely hosted by the ResearchIT
                                                team.
                                            </li>
                                            <li><b>Basic support:</b> Have a quick question about a standard feature?
                                                Click
                                                on the blue
                                                support link and we will do our best to help.
                                            </li>
                                            <li><b>Database backups:</b> REDCap is backed up daily to ensure you do not
                                                lose
                                                your critical
                                                research data
                                            </li>
                                            <li><b>PHI-Approval and HIPAA compliance:</b> REDCap is approved by the
                                                Privacy
                                                office and IRB
                                                for the collection, storage, and analysis of sensitive data containing
                                                PHI.
                                                You must follow
                                                your IRB's recommendations and ensure your project is correctly
                                                configured -
                                                always ask us
                                                if you have questions around security.
                                            </li>
                                            <li><b>Office hours:</b> We offer weekly office hour blocks where basic
                                                questions can be
                                                answered. Office hours are not intended for in-depth project assistance
                                                -
                                                check our our
                                                Support Blocks for that.
                                            </li>
                                        </ul>
                                        <a href="#" class="btn btn-primary text-white">Learn more about our basic
                                            services</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="card">
                                    <!--            <img src="..." class="card-img-top" alt="...">-->
                                    <div class="card-body">
                                        <h5 class="card-title">Supplemental Services</h5>
                                        <p class="card-text">REDCap is a powerful and complex tool. We recommend
                                            budgeting
                                            for and working
                                            with a REDCap expert on your critical projects. We provide a number of
                                            support
                                            and customization
                                            options.
                                        <ul>
                                            <li><b>Support Blocks:</b> work <u>with</u> a dedicated REDCap expert, from
                                                design and planning,
                                                building and testing, to analysis and archival. A support block is
                                                highly
                                                reocmmended for
                                                any complex project where you need a helping hand or more. Leverage our
                                                expertise to save
                                                time, learn more about REDCap, and minimize disruptions.
                                            </li>
                                            <li><b>Professional Services:</b> Let us help you build out the
                                                functionality
                                                you need. Our
                                                team has extensive experience in building custom solutions on top of the
                                                REDCap platform to
                                                offer features not available out-of-the-box.
                                            </li>
                                            <li><b>Code Maintenance:</b> Any custom code or features must be supported
                                                to
                                                ensure it continues
                                                to run going forward. This applies to external modules, custom plugins,
                                                project hooks, or other
                                                related custom solutions.
                                            </li>
                                        </ul>
                                        <a href="#" class="btn btn-primary text-white">Learn more about our supplemental
                                            services</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="snapshot" role="tabpanel" aria-labelledby="snapshot-tab">
                        <div class="row">
                            <div class="col-sm">
                                <div id="em_overview_container">
                                    <p>TODO: Insert table of active EMs with links to documentation</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">

                    </div>
                    <div class="tab-pane fade   show active" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                        <div class="row">
                            <div class="float-right mb-3"><a href="#" class="add-ticket btn btn-primary btn-lg active"
                                                             role="button"
                                                             aria-pressed="true">Add Ticket</a></div>
                        </div>
                        <div class="row">
                            <?php
                            echo $module->getSystemSetting('rit-dashboard-ticket-tab-header');
                            ?>
                        </div>
                        <div class="row">
                            <table id="user-tickets" width="100%" class="table table-bordered table-striped">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Summary</th>
                                    <th scope="col">Request Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created At</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Generic Modal -->

    <div class="modal " id="generic-modal">
        <div class="modal-dialog modal-lg modal-sm">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Create New Ticket</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <?php
                    require_once("create_jira_ticket.php");
                    ?>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="save-ticket btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- END Time Slots Modal -->
    <script src="<?php echo $module->getUrl('assets/js/index.js') ?>"></script>
    <script>
        Main.ajaxCreateJiraTicketURL = "<?php echo $module->getUrl('ajax/create_jira_ticket.php') ?>"
        Main.ajaxUserTicketURL = "<?php echo $module->getUrl('ajax/get_user_tickets.php') ?>"
        Main.init()
    </script>
    <?php
} catch (\Exception $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}