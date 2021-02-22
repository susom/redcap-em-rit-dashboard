<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $module->prepareProjectPortalList();
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

                        if (isset($module->projectPortalSavedConfig['portal_project_id'])) {
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

                    if (isset($module->projectPortalSavedConfig['portal_project_id'])) {
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
                        COPY TO BE ADDED HERE
                        <?php
                        if ($module->getProjectPortalList()) {
                            ?>
                            <select id="project-portal-list" name="project-portal-list">
                                <option value="">SELECT A PROJECT</option>
                                <?php
                                foreach ($module->getProjectPortalList() as $project) {
                                    if ($project['project_deleted_at']) {
                                        continue;
                                    }
                                    ?>
                                    <option data-name="<?php echo $project['project_name'] ?>"
                                            data-description="<?php echo $project['project_description'] ?>"
                                            value="<?php echo $project['id'] ?>" <?php echo(isset($module->projectPortalSavedConfig['portal_project_id']) && $module->projectPortalSavedConfig['portal_project_id'] == $project['id'] ? 'selected' : '') ?>><?php echo $project['project_name'] ?></option>
                                    <?php
                                }
                                ?>
                                <option data-url="<?php echo $module->getPortalBaseURL() ?>" value="">Creat New Research
                                    Portal Project
                                </option>
                            </select>
                            <?php
                        }
                        ?>
                        <button class="btn btn-defaultrc btn-xs fs13" id="attach-redcap-project">Attach Select Project
                        </button>
                            <?php
                            if (isset($module->projectPortalSavedConfig['portal_project_id'])) {
                                ?>
                                <div id="linked-project" data-project-id="<?php echo $project['id'] ?>"><?php
                                    foreach ($module->getProjectPortalList() as $project) {
                                        if ($module->projectPortalSavedConfig['portal_project_id'] == $project['id']) {
                                            echo $project['id'] . '<br>';
                                            echo $project['project_name'] . '<br>';
                                            echo $project['project_description'] . '<br>';
                                            echo '<a target="_blank" href="' . $module->getPortalBaseURL() . 'detail/' . $project['id'] . '">' . $module->getPortalBaseURL() . 'detail/' . $project['id'] . '</a><br>';
                                            echo '<button id="detach-project" data-redcap-id="' . $module->getProjectId() . '" data-portal-project-id="' . $project['id'] . '">Detach from Portal Project</button>';
                                        }

                                    }
                                    ?>
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
    });
</script>
<style>
    /* select2: fixes word text wrap issues on long select values*/
    .select2-container {
        width: 700px !important;;
    }
</style>