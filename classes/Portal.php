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

    private $rmaId;

    /**
     * @var array[]
     */
    public $sprintBlocks = [
        ['id' => 1, 'title' => 'Micro Block - $675', 'price' => 675, 'text' => 'Micro Sprint Block'],
        ['id' => 2, 'title' => 'Small Block - $2,475', 'price' => 2475, 'text' => 'Small Sprint Block'],
        ['id' => 3, 'title' => 'Standard Block - $4,725', 'price' => 4725, 'text' => 'Standard Sprint Block'],
        ['id' => 4, 'title' => 'Large Block - $9,225', 'price' => 9225, 'text' => 'Large Sprint Block'],
    ];

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
        $rma = $this->getREDCapSignedAuthInPortal($portalProjectId, $this->getProjectId());
        if (empty($rma)) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
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


                $rma = $this->getREDCapSignedAuthInPortal($projects['project_id'], $this->getProjectId());
                if (!empty($rma)) {
                    $this->setHasRMA(true);
                    $this->setRMAStatus($rma['status']);
                    $this->setRmaId($rma['id']);
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
    public function getREDCapSignedAuthInPortal($portalProjectId, $redcapProjectId)
    {
        try {
            if (!$portalProjectId) {
                return [];
            }
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/sow/get-signed-auth/', [
                'debug' => false,
                'form_params' => [
                    'redcap_project_id' => $redcapProjectId,
                    'redcap_project_status' => $this->getProjectStatus() ?: DEVELOPMENT_STATUS,
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


    public function getWorkItemsTypes()
    {
        return [];
//        try {
//            $jwt = $this->getClient()->getJwtToken();
//            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/work-items-type/', [
//                'debug' => false,
//                'headers' => [
//                    'Authorization' => "Bearer {$jwt}",
//                ]
//            ]);
//            if ($response->getStatusCode() < 300) {
//                $result = json_decode($response->getBody(), true);
//                return $result['results'];
//            } else {
//                throw new \Exception("could not get REDCap signed auth from portal.");
//            }
//        } catch (\Exception $e) {
//            return array();
//        }
    }

    /**
     * @param $type
     * @return array|mixed
     */
    public function searchWorkItemType($type)
    {
        $types = $this->getWorkItemsTypes();
        foreach ($this->getWorkItemsTypes() as $itemsType) {
            if ($type == $itemsType['name']) {
                return $itemsType;
            }
        }
        return [];
    }

    /**
     * @param $id
     * @return array
     */
    public function searchServiceBlock($id)
    {
        foreach ($this->sprintBlocks as $sprintBlock) {
            if ($id == $sprintBlock['id']) {
                return $sprintBlock;
            }
        }
        return [];
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

    /**
     * this method will replicate records from redcap_entity_external_modules_charges on redcap to app_redcapemcharges
     * on r2p2
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function replicateREDCapEMCharges()
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/projects/redcap/charges/replicate/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                // when replicating is complete lets generate charges for newly replicated records.
                $this->processRMACharges();
                $result = json_decode($response->getBody(), true);
                return $result['results'];
            } else {
                throw new \Exception("could not get REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            return array();
        }
    }

    public function pushChargesToR2P2($charges)
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/redcap/custom-charges/push/', [
                'debug' => false,
                'form_params' => [
                    'charges' => json_encode($charges, JSON_FORCE_OBJECT),
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                // when replicating is complete lets generate charges for newly replicated records.
                $this->processRMACharges();
                $result = json_decode($response->getBody(), true);
                return $result['results'];
            } else {
                throw new \Exception("could not get REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * this method will generate app_chargeitems records for
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processRMACharges()
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/projects/sow/charges/process-rma/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                $result = json_decode($response->getBody(), true);
                return $result['results'];
            } else {
                throw new \Exception("could not get REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            return array();
        }
    }

    public function generateR2P2SOW($portalProjectId, $redcapProjectId, $workItems)
    {
        try {
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/sow/generate/', [
                'debug' => false,
                'form_params' => [
                    'user' => USERID,
                    'redcap_project_id' => $redcapProjectId,
                    'description' => '',
                    'work_items' => json_encode($workItems),
                    'title' => $this->projectPortalSavedConfig['portal_project_name'] . ' - Request for ' . $workItems[0]['text']
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            } else {
                throw new \Exception("could not generate SOW.");
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
                'portal_project_sow_url' => $this->getClient()->getPortalBaseURL() . 'detail/' . $portalProjectId . '/sow',
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

    /**
     * @return mixed
     */
    public function getRmaId()
    {
        return $this->rmaId;
    }

    /**
     * @param mixed $rmaId
     */
    public function setRmaId($rmaId)
    {
        $this->rmaId = $rmaId;
    }


}