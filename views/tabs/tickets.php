<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
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
