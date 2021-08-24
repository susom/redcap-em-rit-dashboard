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
            <div id="portal-project"></div>
        </div>

    </div>
    <div class="row">
        <div class="offset-3 mt-2">
            <button class="btn btn-defaultrc btn-xs fs13" id="attach-redcap-project">Attach
                Select Project
            </button>
        </div>
    </div>
</div>