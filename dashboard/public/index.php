<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Test Page</title>
    <link href="<?php echo $module->getUrl('dashboard/dist/app.css') . '?t=' . time();; ?>" rel="preload" as="style">
    <link href="<?php echo $module->getUrl('dashboard/dist/app.js') . '?t=' . time();; ?>" rel="preload" as="script">
    <link href="<?php echo $module->getUrl('dashboard/dist/chunk-vendors.css') . '?t=' . time();; ?>" rel="preload"
          as="style">
    <link href="<?php echo $module->getUrl('dashboard/dist/chunk-vendors.js') . '?t=' . time();; ?>" rel="preload"
          as="script">
    <link href="<?php echo $module->getUrl('dashboard/dist/app.css') . '?t=' . time();; ?>" rel="stylesheet">
</head>
<body>
<noscript>
    <strong>We're sorry but <%= htmlWebpackPlugin.options.title %> doesn't work properly without JavaScript enabled.
        Please enable it to continue.</strong>
</noscript>
<app pid="<?php echo $module->getProjectId() ?>"></app>
<!-- built files will be auto injected -->
<script src="<?php echo $module->getUrl('dashboard/dist/chunk-vendors.js') . '?t=' . time();; ?>"></script>
<script src="<?php echo $module->getUrl('dashboard/dist/app.js') . '?t=' . time(); ?>"></script>
</body>
</html>
