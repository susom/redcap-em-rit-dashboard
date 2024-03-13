<?php

namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */
try{

//    if(!$_POST){
//        throw new \Exception("No POST Found!");
//    }

    $list = $module->getNotificationProjects();
    header("Content-type: application/json");
    echo json_encode($list);

}catch (\Exception $e){
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}