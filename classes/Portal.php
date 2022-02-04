<?php


namespace Stanford\ProjectPortal;


/**
 * Class Portal
 * @package Stanford\ProjectPortal
 * @property \Stanford\ProjectPortal\Client $client
 * @property array $projectPortalList
 * @property int $projectId
 * @property bool $projectStatus
 * @property string $projectTitle
 * @property array $projectPortalSavedConfig
 * @property boolean $hasRMA
 * @property int $RMAStatus
 */
class Portal
{
    private $client;

    private $projectPortalList;

    private $projectId;

    private $projectStatus;

    private $projectTitle;

    public $projectPortalSavedConfig;

    private $hasRMA;

    private $RMAStatus;

    /**
     * User constructor.
     * @param \Stanford\ProjectPortal\Client $client
     */
    public function __construct(Client $client, $projectId = null, $projectTitle = null, $projectStatus = null)
    {
        $this->setClient($client);

        $this->setProjectId($projectId);

        $this->setProjectTitle($projectTitle);

        $this->setProjectStatus($projectStatus);


    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function prepareR2P2SavedProject()
    {
        try {
            if (!is_null($this->getProjectId())) {
                $this->setProjectPortalSavedConfig($this->getProjectId());
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function updateRMA($portalProjectId)
    {
        $rma = $this->getREDCapSignedAuthInPortal($portalProjectId, $this->getProjectId(), $this->getProjectStatus());

        $jwt = $this->getClient()->getJwtToken();
        $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/projects/sow/' . $rma['id'] . '/update-work-items/', [
            'debug' => false,
            'headers' => [
                'Authorization' => "Bearer {$jwt}",
            ]
        ]);
        if ($response->getStatusCode() < 300) {
            return json_decode($response->getBody(), true);
        } else {
            throw new \Exception("could not get REDCap signed auth from portal.");
        }
    }

    /**
     * @param $redcapProjectId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setProjectPortalSavedConfig($redcapProjectId)
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/redcap/search/' . $redcapProjectId . '/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                $projects = json_decode($response->getBody(), true);
            } else {
                throw new \Exception("could not get REDCap signed auth from portal.");
            }
            if (!empty($projects)) {
                //$projects = json_decode($projects, true);
                // temp fix for now
                $projects = $projects[0];
                $this->projectPortalSavedConfig['portal_project_id'] = $projects['project_id'];
                $this->projectPortalSavedConfig['portal_project_name'] = $projects['portal_project_name'];
                $this->projectPortalSavedConfig['portal_project_description'] = $projects['portal_project_description'];
                $this->projectPortalSavedConfig['portal_project_url'] = $this->getClient()->getPortalBaseURL() . 'detail/' . $projects['project_id'];


                $rma = $this->getREDCapSignedAuthInPortal($projects['project_id'], $this->getProjectId(), $this->getProjectStatus());
                if (!empty($rma)) {
                    $this->setHasRMA(true);
                    $this->setRMAStatus($rma['status']);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * @param int $portalProjectId
     * @param int $redcapProjectId
     * @param int $redcapStatus
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getREDCapSignedAuthInPortal($portalProjectId, $redcapProjectId, $redcapStatus = 0)
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/sow/get-signed-auth/', [
                'debug' => false,
                'form_params' => [
                    'redcap_project_id' => $redcapProjectId,
                    'redcap_project_status' => $redcapStatus,
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            } else {
                throw new \Exception("could not get REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            return array();
        }
    }


    /**
     * @param int $portalProjectId
     * @param int $redcapProjectId
     * @param int $redcapStatus
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRMALineItems($portalProjectId, $rmaID, $userId)
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $url = $this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/sow/' . $rmaID . '/work-items/';
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/sow/' . $rmaID . '/work-items/', [
                'debug' => false,
                'form_params' => [
                    'user' => $userId,
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            } else {
                throw new \Exception("could not get REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            return array();
        }
    }


    public function generateREDCapSignedAuthInPortal($portalProjectId, $redcapProjectId, $external_modules, $username, $overdue = '')
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/sow/generate-signed-auth/', [
                'debug' => false,
                'form_params' => [
                    'redcap_project_id' => $redcapProjectId,
                    'external_modules' => $external_modules,
                    'overdue_payment' => $overdue,
                    'username' => $username,
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            } else {
                throw new \Exception("could not generate REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            throw new \LogicException($e->getMessage());
        }
    }

    public function appendApprovedREDCapSignedAuthInPortal($portalProjectId, $redcapProjectId, $portalSOWID, $external_modules, $username, $overdue = '')
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/sow/append-signed-auth/', [
                'debug' => false,
                'form_params' => [
                    'redcap_project_id' => $redcapProjectId,
                    'portal_sow_id' => $portalSOWID,
                    'username' => $username,
                    'external_modules' => $external_modules,
                    'overdue_payment' => $overdue,
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            } else {
                throw new \Exception("could not generate REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            throw new \LogicException($e->getMessage());
        }
    }

    /**
     * call django endpoint to attach redcap project to portal project. need more testnig
     * @param $projectId
     * @throws \Exception
     */
    public function detachPortalProject($portalProjectId, $redcapProjectId)
    {
        try {
//            $this->setProject(new \Project($this->getProjectId()));
            //$this->getProjectPortalJWTToken();

            $client = $this->getClient()->getGuzzleClient();
            $jwt = $this->getClient()->getJwtToken();
            $response = $client->delete($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/detach-redcap/' . $redcapProjectId . '/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
//            if ($response->getStatusCode() < 300) {
//                $data = json_decode($response->getBody());
//                $this->setProjectPortalList(json_decode(json_encode($data), true));
//            }

            $inputs = array(
                'portal_project_id' => '',
                'portal_project_name' => '',
                'portal_project_description' => '',
                'portal_project_url' => ''
            );
            return $inputs;
        } catch (\Exception $e) {
            throw new \LogicException($e->getMessage());
        }
    }

    /**
     * call django endpoint to attach redcap project to portal project. need more testnig
     * @param $projectId
     * @throws \Exception
     */
    public function attachToProjectPortal($portalProjectId, $portalProjectName, $portalProjectDescription): array
    {
        try {
            //$this->getProjectPortalJWTToken();

            if (is_null($portalProjectId) || $portalProjectId == '') {
                throw new \Exception('R2P2 project cant be empty please select a project from the dropdown or create new one.');
            }
            $client = $this->getClient()->getGuzzleClient();
            $jwt = $this->getClient()->getJwtToken();
            $response = $client->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/attach-redcap/', [
                'debug' => false,
                'form_params' => [
                    'redcap_project_id' => $this->getProjectId(),
                    'redcap_project_name' => $this->getProjectTitle(),
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
//            if ($response->getStatusCode() < 300) {
//                $data = json_decode($response->getBody());
//                $this->setProjectPortalList(json_decode(json_encode($data), true));
//            }

            $inputs = array(
                'portal_project_id' => $portalProjectId,
                'portal_project_name' => $portalProjectName,
                'portal_project_description' => $portalProjectDescription,
                'portal_project_url' => $this->getClient()->getPortalBaseURL() . 'detail/' . $portalProjectId,
            );
            return $inputs;
        } catch (\Exception $e) {
            throw new \LogicException($e->getMessage());
        }
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }


    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @param int|null $projectId
     */
    public function setProjectId($projectId): void
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getProjectTitle(): string
    {
        return $this->projectTitle;
    }

    /**
     * @param string|null $projectTitle
     */
    public function setProjectTitle($projectTitle): void
    {
        $this->projectTitle = $projectTitle;
    }

    /**
     * @return mixed
     */
    public function getHasRMA()
    {
        return $this->hasRMA;
    }

    /**
     * @param mixed $hasRMA
     */
    public function setHasRMA($hasRMA): void
    {
        $this->hasRMA = $hasRMA;
    }

    /**
     * @return mixed
     */
    public function getRMAStatus()
    {
        return $this->RMAStatus;
    }

    /**
     * @param mixed $RMAStatus
     */
    public function setRMAStatus($RMAStatus): void
    {
        $this->RMAStatus = $RMAStatus;
    }

    /**
     * @return mixed
     */
    public function getProjectStatus()
    {
        return $this->projectStatus;
    }

    /**
     * @param mixed $projectStatus
     */
    public function setProjectStatus($projectStatus): void
    {
        $this->projectStatus = $projectStatus;
    }


}