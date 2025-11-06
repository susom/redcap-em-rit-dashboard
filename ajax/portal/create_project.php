<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $project['project_name'] = htmlentities($body['project_portal_name']);
    $project['project_description'] = htmlentities($body['project_portal_description']);
    $project['project_type'] = htmlentities($body['project_portal_type']);
    if (isset($body['irb_number'])) {
        $project['irb_number'] = htmlentities($body['irb_number']);
    } else {
        $project['irb_number'] = '';
    }
    if (!$project['project_type']) {
        throw new \Exception('Project type is missing');
    }
    if (!$project['project_description']) {
        throw new \Exception('Project description is missing');
    }
    if (!$project['project_name']) {
        throw new \Exception('Project name is missing');
    }
    $data = $module->getPortal()->createProject($project);
    if (!empty($data)) {
        // add current user to newly created project.
        $user = $module->framework->getUser();
        $username = $user->getUsername();
        $user = ExternalModules::getUserInfo($username);
        $module->getPortal()->addProjectUser($data['id'], $user);

        echo json_encode(array_merge($data, array('status' => 'success', 'project_portal_id' => $data['id'])));
    } else {
        echo json_encode(array('status' => 'empty'));
    }
} catch (ClientException $e) {
    header("Content-type: application/json");
    http_response_code(404);
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();
    $message = json_decode($responseBodyAsString, true);
    echo json_encode(array('status' => 'error', 'message' => $message['message']));
}
catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>