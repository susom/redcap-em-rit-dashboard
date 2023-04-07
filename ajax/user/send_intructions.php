<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

try {
    $body = json_decode(file_get_contents('php://input'), true);


    if (!$body['sow_approval_emails']) {
        throw new \Exception('Emails are missing');
    }

    if (!$body['template']) {
        throw new \Exception('Template are missing');
    }

    $template = $module->getEmailTemplates($body['template']);
    if (empty($template)) {
        throw new \Exception($body['template'] . ' is not configured');
    }

    $emails = explode(',', $body['sow_approval_emails']);
    foreach ($emails as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception($email . ' is not a valid email');
        }
    }

    $module->sendEmail($emails, $template['template_subject'], $template['template_body']);
    echo json_encode(array('status' => 'success', 'message' => 'Emails were sent successfully!'));
} catch (\Exception $e) {
    header("Content-type: application/json");
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>