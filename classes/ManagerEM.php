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
    private $managerEM;

    private $client;

    private $prefix;

    public function __construct($client, $prefix)
    {
        $this->setPrefix($prefix);
        if (ExternalModules::getSystemSetting($this->getPrefix(), 'external-modules-manager-em') != '') {
            $this->setManagerEM(ExternalModules::getModuleInstance(ExternalModules::getSystemSetting($this->getPrefix(), 'external-modules-manager-em')));
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
                $url = $this->getManagerEM()->getUrl('ajax/refresh_project_em_util.php', true, true) . '&redcap_project_id=' . $projectId . '&pid=' . $this->getManagerREDCapProjectId();
                $response = $this->getClient()->getGuzzleClient()->get($url, [
                    'debug' => false
                ]);
                if ($response->getStatusCode() < 300) {
                    return json_decode($response->getBody(), true);
                }
            }
        } catch (\Exception $e) {
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
    public function getManagerEM()
    {
        return $this->managerEM;
    }

    /**
     * @param \Stanford\ExternalModuleManager\ExternalModuleManager $managerEM
     */
    public function setManagerEM($managerEM): void
    {
        $this->managerEM = $managerEM;
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