<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>dashboard</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $module->getUrl("dashboard/dist/static/css/app.css", true, true)?>"/>
  </head>
  <body>
    <div id="app">
      <App pid="123123"></App>
      <Test>dsad</Test>
    </div>
    <!-- built files will be auto injected -->
    <script src="<?php echo $module->getUrl("dashboard/dist/static/js/manifest.js", true, true)?>"></script>
    <script src="<?php echo $module->getUrl("dashboard/dist/static/js/vendor.js", true, true)?>"></script>

    <script src="<?php echo $module->getUrl("dashboard/dist/static/js/app.js", true, true)?>"></script>
  </body>
</html>
