<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
<div class="row">
    <?php
    echo $module->getSystemSetting('rit-dashboard-portal-linkage-tab-header');
    ?>
</div>
<div class="row">
    <div class="row">
        <div class="col-3">
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
        </div>
        <div class="col-9">
            <b-form-select v-model="ticket.project_portal_id" class="mb-3">
                <?php
                foreach ($module->getUser()->getProjectPortalList() as $project) {
                    if ($project['project_deleted_at']) {
                        continue;
                    }
                    ?>
                    <b-form-select-option ref="selectedProject" data-name="<?php echo $project['project_name'] ?>"
                                          data-description="<?php echo $project['project_description'] ?>"
                                          value="<?php echo $project['id'] ?>"><?php echo $project['project_name'] ?></b-form-select-option>

                    <?php
                }
                ?>
            </b-form-select>
        </div>

    </div>
    <div class="row">
        <div class="offset-3 mt-2">
            <b-button v-if="linked() == false" @click="attachRedCapProject()" variant="success">Attache Selected
                Project
            </b-button>
            <b-button v-else @click="detachRedCapProject()" variant="danger">Detach Selected Project</b-button>
        </div>
    </div>
</div>