<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
<script>
    window.notifications = <?php echo json_encode($module->getNotifications()) ?>;
    window.ajax_urls = <?php echo json_encode($module->getAjaxFiles(__DIR__ . '/../ajax/', 'ajax')) ?>;
</script>
<div id="app"></div>
<script src="<?php echo $module->getUrl('frontend_3/public/js/bundle.js') ?>"></script>

<link rel="stylesheet" href="<?php echo $module->getUrl('frontend_3/dist/css/app.css') ?>"/>


