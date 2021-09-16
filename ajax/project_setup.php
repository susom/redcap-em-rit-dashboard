<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    ?>
    <div id="portal-linkage-container" class="round chklist col-12">
        <div id="portal-errors" class="alert alert-danger hidden"></div>
        <table cellspacing="0" width="100%">
            <tbody>
            <tr>
                <td valign="top" style="width:70px;text-align:center;">
                    <!-- Icon -->
                    <div>
                        <?php
                        if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
                            ?>
                            <img id="img-" src="<?php echo APP_PATH_WEBROOT ?>Resources/images/checkbox_checked.png">
                            <?php
                        } else {
                            ?>
                            <img id="img-" src="<?php echo APP_PATH_WEBROOT ?>Resources/images/checkbox_cross.png">
                            <?php
                        }
                        ?>
                    </div>
                    <?php

                    if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
                        ?>
                        <div id="lbl-" style="color:green;"> Linked</div>
                        <?php
                    } else {
                        ?>
                        <div id="lbl-" style="color:#F47F6C;">Not Linked</div>
                        <?php
                    }
                    ?>

                    <!-- "I'm done!" button OR "Not complete?" link -->
                </td>
                <td valign="top" style="padding-left:30px;">
                    <div class="chklisthdr">
                        <span>Link Your REDCap Project to RIT Research Project Portal</span>
                    </div>
                    <div class="chklisttext">
                        <?php
                        if (isset($module->getPortal()->projectPortalSavedConfig['portal_project_id'])) {
                            ?>
                            <div id="linked-project" data-project-id="<?php echo $module->getProjectId() ?>"><?php
                                foreach ($module->getUser()->getProjectPortalList() as $project) {
                                    if ($module->getPortal()->projectPortalSavedConfig['portal_project_id'] == $project['id']) {
                                        echo 'This project is part of <a class="btn btn-primary" target="_blank" href="' . $module->getClient()->getPortalBaseURL() . 'detail/' . $project['id'] . '">' . $project['project_name'] . '</a><br>';
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
                                    is this?</a> <br>Link now with the <a class="btn btn-success"
                                                                          href="<?php echo $module->getUrl("views/index.php") ?>">My
                                    Research IT Dashboard</a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
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