<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $module->prepareProjectPortalList();
    ?>
    <div id="portal-linkage-container" class="round chklist col-12">
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
                            </select>
                            <?php
                        }
                        ?>
                        <button class="btn btn-defaultrc btn-xs fs13" id="attach-redcap-project">Attach Select Project
                        </button>
                        <div id="linked-project" data-project-id="<?php echo $project['id'] ?>"><?php
                            if (isset($module->projectPortalSavedConfig['portal_project_id'])) {
                                foreach ($module->getProjectPortalList() as $project) {
                                    if ($module->projectPortalSavedConfig['portal_project_id'] == $project['id']) {
                                        echo $project['id'] . '<br>';
                                        echo $project['project_name'] . '<br>';
                                        echo $project['project_description'] . '<br>';
                                        echo '<a target="_blank" href="' . $module->getPortalBaseURL() . 'detail/' . $project['id'] . '">' . $module->getPortalBaseURL() . 'detail/' . $project['id'] . '</a><br>';
                                    }

                                }
                            }
                            ?></div>
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
    });
</script>
<style>
    /* select2: fixes word text wrap issues on long select values*/
    .select2-container {
        width: 700px !important;;
    }
</style>