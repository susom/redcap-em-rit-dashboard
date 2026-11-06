<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;

/**
 * Class ManagerEM
 * @property \Stanford\ProjectPortal\Client $client
 * @property \Stanford\ExternalModuleManager\ExternalModuleManager $managerEM
 */
class ManagerEM
{

    private $managerREDCapProjectId;

    private $managerEMObject;

    private $client;

    private $prefix;

    public function __construct($client, $prefix)
    {
        $this->setPrefix($prefix);
        if (ExternalModules::getSystemSetting($this->getPrefix(), 'external-modules-manager-em') != '') {
            $this->setManagerEMObject(ExternalModules::getModuleInstance(ExternalModules::getSystemSetting($this->getPrefix(), 'external-modules-manager-em')));
        }
        if (ExternalModules::getSystemSetting($this->getPrefix(), 'external-modules-manager-redcap-project') != '') {
            $this->setManagerREDCapProjectId(ExternalModules::getSystemSetting($this->getPrefix(), 'external-modules-manager-redcap-project'));
        }
        $this->setClient($client);

    }

    public function updateProjectEMUtil($projectId)
    {
        try {
            if ($this->getManagerREDCapProjectId() != '') {
                $managerEM = $this->getManagerEMObject();
                $url = $managerEM->getUrl('ajax/refresh_project_em_util.php', true, true) . '&redcap_project_id=' . intval($projectId) . '&pid=' . intval($this->getManagerREDCapProjectId());
                // EM Manager only runs this NOAUTH page for callers that POST its shared api-token.
                // The token goes in the body, never the URL, so it stays out of access logs.
                $response = $this->getClient()->getGuzzleClient()->post($url, [
                    'form_params' => ['secret_token' => (string)$managerEM->getSystemSetting('api-token')],
                    'debug' => false
                ]);
                if ($response->getStatusCode() < 300) {
                    return json_decode($response->getBody(), true);
                }
            }
        } catch (\Exception $e) {
            // Callers (go_prod's RMA rule, generate_rma) carry on with the stored usage rows, so leave a trace.
            ExternalModules::getModuleInstance($this->getPrefix())->emError("Refreshing EM usage for project $projectId failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }


    /**
     * @return \Stanford\ExternalModuleManager\ExternalModuleManager
     */
    public function getManagerEMObject()
    {
        return $this->managerEMObject;
    }

    /**
     * @param \Stanford\ExternalModuleManager\ExternalModuleManager $managerEM
     */
    public function setManagerEMObject($managerEM): void
    {
        $this->managerEMObject = $managerEM;
    }

    /**
     * @return \Stanford\ProjectPortal\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Stanford\ProjectPortal\Client $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getManagerREDCapProjectId()
    {
        return $this->managerREDCapProjectId;
    }

    /**
     * @param mixed $managerREDCapProjectId
     */
    public function setManagerREDCapProjectId($managerREDCapProjectId): void
    {
        $this->managerREDCapProjectId = $managerREDCapProjectId;
    }


}