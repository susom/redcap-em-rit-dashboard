<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    ?>
    <!--    <style>-->
    <!--        portal-setupx.a {-->
    <!--            text-decoration: underline;-->
    <!--        }-->
    <!--    </style>-->
    <div id="portal-linkage-container" class="mb-2"
         data-is-linked="<?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) ? 'true' : 'false' ?>"">
    <div id="portal-errors" class="alert alert-danger hidden"></div>
    <div class="rounded alert alert-<?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) ? 'success' : 'danger' ?>">
        <div class="row">
            <div class="col-2">
                <div class="row">
                    <?php
                    if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
                        ?>
                        <i style="font-size: 20px; margin-left: 20%;" class="fas fa-check"></i>
                        <?php
                    } else {
                        ?>
                        <i style="font-size: 30px; margin-left: 20%;" class="fas fa-times"></i>
                        <?php
                    }
                    ?>
                </div>

            </div>
            <div class="col-8">
                <div class="row">
                    <?php
                    // If the PID is associated to a REDCap Project
                    if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
                        ?>
                        <div class="row">
                            <div class="">
                                    <span><!--i class="fas fa-wrench"></i--> This REDCap project is part of the R2P2 Project
                                        <br><a style="text-decoration: underline" class="portal-setupx" target="_blank"
                                               href="<?php echo $module->getPortal()->projectPortalSavedConfig['portal_project_url'] ?>">

                                            <i class="fas fa-external-link-alt"></i> <span><?php echo $module->getPortal()->projectPortalSavedConfig['portal_project_name'] ?></span>
                                        </a>
                                    </span>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div>
                            This REDCap project is NOT yet linked to an R2P2 project.
                            <!--                                <a style="text-decoration: underline" id="what-is-this" href="#">What is this?</a>-->
                            <br> Click the
                            <a class="portal-setup" href="<?php echo $module->getUrl("views/index.php") ?>">
                                <i class="fas fa-column"></i> <span>REDCap R2P2 Dashboard</span>
                            </a> link on the left sidebar to get started.
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="col-2">
                <div class="text-right" style="color:#555; font-size:11px;">
                    <a id="what-is-this" href="#"><i class="fas fa-info-circle"></i> <span
                                style="text-decoration: underline">What is R2P2?</span></a>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
        $data = $module->getPortal()->getREDCapSignedAuthInPortal($module->getPortal()->projectPortalSavedConfig['portal_project_id'], $module->getProjectId());
        $statuses = [2, 6, 7];
        if (empty($data) && !in_array($data['status'], $statuses)) {
            ?>
            <div class="rounded alert alert-<?php echo($module->getProject()->project['status'] == '1' ? 'danger' : 'warning') ?>">
                <div class="row">
                    <div class="col-2">
                        <div class="row"><i style="font-size: 30px; margin-left: 20%;" class="fas fa-times"></i></div>
                    </div>
                    <div class="col-8">
                        <div class="row">
                            <div class="row">
                                <div class="">
                                    <?php
                                    if ($module->getProject()->project['status'] == '1') {
                                        ?>
                                        <span><?php echo $module->getNotifications()['get_project_ems_prod'] ?></span>
                                        <?php
                                    } else {
                                        ?>
                                        <span><?php echo $module->getNotifications()['get_project_ems_dev'] ?></span>
                                        <?php
                                    }
                                    ?>
                                    <br>If RMA in place please make sure its approved. otherwise Click the
                                    <a class="portal-setup" href="<?php echo $module->getUrl("views/index.php") ?>">
                                        <i class="fas fa-external-link-alt"></i> <span>REDCap R2P2 Dashboard</span>
                                    </a> link on the left sidebar to generate RMA.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div data-status="<?php echo $data['status'] ?>" class="rounded alert alert-success">
                <div class="row">
                    <div class="col-2">
                        <div class="row"><i style="font-size: 20px; margin-left: 20%;" class="fas fa-check"></i></div>
                    </div>
                    <div class="col-8">
                        <div class="row">
                            <div class="row">
                                <div class="">
                                    <h5
                                            class="d-inline-block  p-1"></h5>This REDCap Project is linked to
                                    an
                                    approved
                                    REDCap Maintenance Agreement.

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
    </div>
    <?php
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}
?>
<div id="what-is-this-dialog" style="top: 10% !important; display: none" title="What is R2P2?">
    <div>
        R2P2 (<u>R</u>esearch IT <u>R</u>esearch <u>P</u>roject <u>P</u>ortal) is a web platform that coordinates
        applications, services, and support for researchers working with the Research IT team and Stanford Technology
        and
        Digital Solutions (TDS).
    </div>
    <div>
        You can interact with R2P2 by clicking on the <a class="portal-setup"
                                                         href="<?php echo $module->getUrl("views/index.php") ?>">
            <i class="fas fa-column"></i> <span>My REDCap R2P2 Dashboard</span>
        </a> link on the left sidebar.
    </div>
    <div>For more information, please check out the <a style="text-decoration:underline"
                                                       href="https://rit-portal.med.stanford.edu/faq" target="_blank">
            <i class="fas fa-external-link-alt"></i> <span>R2P2 FAQ</span></a>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#project-portal-list').select2();
        $('#project-portal-list').on('select2:select', function (e) {
            // if user selects create new project open new tab with to the portal.
            if ($("#project-portal-list").select2().find(":selected").data("url") !== undefined) {
                var url = $("#project-portal-list").select2().find(":selected").data("url")
                window.open(url, '_blank');
            }
        });

        $(document).on('click', '#what-is-this', function (e) {
            $('#what-is-this-dialog').dialog({
                bgiframe: true, modal: true, width: 400, position: ['center', 20],
                open: function () {
                    fitDialog(this);
                },
                buttons: {
                    Cancel: function () {
                        $(this).dialog('close');
                    },
                    // Link: function () {
                    //     $("#project-portal-list").select2('open')
                    //     $(this).dialog('close');
                    // }
                }
            });
        })
    });
</script>
<style>
    /* select2: fixes word text wrap issues on long select values*/
    .select2-container {
        width: 700px !important;;
    }
</style>