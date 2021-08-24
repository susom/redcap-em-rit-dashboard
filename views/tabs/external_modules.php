<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
<div class="row">
    <?php
    echo $module->getSystemSetting('rit-dashboard-external-modules-tab-header');
    ?>
</div>
<div class="row">
    <table id="external-modules-table" width="100%" class="table table-bordered table-striped">
        <thead class="thead-light">
        <tr>
            <th scope="col">Modules Prefix</th>
            <th scope="col">Is Enabled</th>
            <th scope="col">Maintenance Fees</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot>
        <tr>
            <th colspan="2" style="text-align:right">Total:</th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>
