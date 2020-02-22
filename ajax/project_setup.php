<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $module->prepareProjectPortalList();
    ?>
    <div class="round chklist col-12">
        <table cellspacing="0" width="100%">
            <tbody>
            <tr>
                <td valign="top" style="width:70px;text-align:center;">
                    <!-- Icon -->
                    <div>
                        <?php

                        if (isset($module->projectPortalSavedConfig['portal-project-id'])) {
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

                    if (isset($module->projectPortalSavedConfig['portal-project-id'])) {
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
                            <select name="project-portal-list">
                                <option value="">SELECT A PROJECT</option>
                                <?php
                                foreach ($module->getProjectPortalList() as $project) {
                                    ?>
                                    <option value="<?php echo $project['id'] ?>" <?php echo(isset($module->projectPortalSavedConfig['portal-project-id']) && $module->projectPortalSavedConfig['portal-project-id'] == $project['id'] ? 'selected' : '') ?>><?php echo $project['project_name'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <?php
                        }
                        ?>
                        <pre>
                            <?php
                            if (isset($module->projectPortalSavedConfig['portal-project-id'])) {
                                foreach ($module->getProjectPortalList() as $project) {
                                    if ($module->projectPortalSavedConfig['portal-project-id'] == $project['id']) {
                                        print_r($project);
                                    }

                                }
                            }
                            ?>
                        </pre>
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