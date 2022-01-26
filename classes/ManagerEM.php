<?php

namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;

/**
 *
 */
class ManagerEM
{

    private $managerEM;

    private $prefix;

    public function __construct($prefix)
    {
        $this->setPrefix($prefix);

        $this->setManagerEM(ExternalModules::getModuleInstance(ExternalModules::getSystemSetting($this->getPrefix(), 'external-modules-manager-em')));

    }

    public function updateProjectEMUtil($projectId)
    {
        try {
            $this->getManagerEM()->refreshProjectEMUsage($projectId);
            return true;
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
     * @return mixed
     */
    public function getManagerEM()
    {
        return $this->managerEM;
    }

    /**
     * @param mixed $managerEM
     */
    public function setManagerEM($managerEM): void
    {
        $this->managerEM = $managerEM;
    }


}