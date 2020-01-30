<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $module->processRequest();
} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (\Exception $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}

