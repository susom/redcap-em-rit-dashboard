<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    ?>
    <style>
        a:hover {
            text-decoration: none !important;
        }
    </style>
    <div id="portal-linkage-container">
        <div id="portal-errors" class="alert alert-danger hidden"></div>
        <div class="rounded alert alert-<?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) ? 'success' : 'danger' ?>">

            <div class="row">
                <div class="col-2">
                    <div class="row">
                        <?php
                        if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
                            ?>
                            <i style="font-size: 50px; margin-left: 20%;" class="fas fa-check"></i>
                            <?php
                        } else {
                            ?>
                            <i style="font-size: 55px; margin-left: 20%;" class="fas fa-times"></i>
                            <?php
                        }
                        ?>
                    </div>

                </div>
                <div class="col-10">
                    <div class="row">
                        <div class="chklisthdr">
                            <span><i class="fas fa-wrench"></i> Link Your REDCap Project to RIT Research Project Portal</span>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
                            ?>
                            <div id="linked-project" data-project-id="<?php echo $module->getProjectId() ?>"><?php
                                foreach ($module->getUser()->getProjectPortalList() as $project) {
                                    if ($module->getPortal()->projectPortalSavedConfig['portal_project_id'] == $project['id']) {
                                        echo 'This project is part of <a class="portal-setup" target="_blank" href="' . $module->getClient()->getPortalBaseURL() . 'detail/' . $project['id'] . '"> <span class="btn-xs btn-success">' . $project['project_name'] . '</span></a><br>';
//                                        echo '<button id="detach-project" data-redcap-id="' . $module->getProjectId() . '" data-portal-project-id="' . $project['id'] . '">Detach from Portal Project</button>';
                                        break;
                                    }

                                }
                                ?>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div>
                                This project is NOT linked to the Research IT Portal. <a id="what-is-this" href="#">What
                                    is this?</a> <br>Link now with the <a class="portal-setup"
                                                                          href="<?php echo $module->getUrl("views/index.php") ?>"><span
                                            class="btn btn-success">My
                                    Research IT Dashboard</span></a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
} catch (\LogicException $e) {
    echo "<div class='alert-danger'>" . $e->getMessage() . "</div>";
}
?>
<div id="what-is-this-dialog" style="top: 10% !important; display: none"
     title="What is Research IT Project Portal">The Research IT Portal is a web platform that is responsible for
    tracking, communicating, and supporting research projects affiliated with Stanford TDS.<a
            href="https://rit-portal.med.stanford.edu" target="_blank">https://rit-portal.med.stanford.edu</a> and
    create your research project.</d></div>
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