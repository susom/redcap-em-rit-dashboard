<?php


namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;

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

    private $groups = [];
    const NEW_PTA = array('id' => 'new', 'pta_charge_number' => '**Create New PTA**');
    /**
     * @var array[]
     */
    public $sprintBlocks = [
        ['id' => 1, 'title' => 'Micro Block - $900', 'price' => 900, 'text' => 'Micro Sprint Block'],
        ['id' => 5, 'title' => 'Mini Block - $1,500', 'price' => 1500, 'text' => 'Mini Sprint Block'],
        ['id' => 6, 'title' => 'Extra Small Block - $2,100', 'price' => 2100, 'text' => 'Extra Small Sprint Block'],
        ['id' => 2, 'title' => 'Small Block - $3,300', 'price' => 3300, 'text' => 'Small Sprint Block'],
        ['id' => 3, 'title' => 'Standard Block - $6,300', 'price' => 6300, 'text' => 'Standard Sprint Block'],
        ['id' => 4, 'title' => 'Large Block - $12,300', 'price' => 12300, 'text' => 'Large Sprint Block'],
    ];

    public $discountedSprintBlocks = [
        ['id' => 1, 'title' => 'Micro Block - $705', 'price' => 705, 'text' => 'Micro Sprint Block'],
        ['id' => 5, 'title' => 'Mini Block - $1,175', 'price' => 1175, 'text' => 'Mini Sprint Block'],
        ['id' => 6, 'title' => 'Extra Small Block - $1,645', 'price' => 1645, 'text' => 'Extra Small Sprint Block'],
        ['id' => 2, 'title' => 'Small Block - $2,585', 'price' => 2585, 'text' => 'Small Sprint Block'],
        ['id' => 3, 'title' => 'Standard Block - $4,935', 'price' => 4935, 'text' => 'Standard Sprint Block'],
        ['id' => 4, 'title' => 'Large Block - $9,635', 'price' => 9635, 'text' => 'Large Sprint Block'],
    ];

    public $fundingSources = [
        ['id' => 1,'label' =>'Federally Funded, e.g., NIH grant'],
        ['id' => 2,'label' =>'Federally Funded, e.g., NIH grant'],
        ['id' => 3,'label' =>'Industry Funded'],
        ['id' => 4,'label' =>'Stanford Department/ Gift Funded'],
        ['id' => 5,'label' =>'Seeking Funding, e.g., grant to be written'],
        ['id' => 6,'label' =>'Unfunded'],
    ];

    const ADMIN_GROUP_ID = 3;

    const APPROVED_PENDING_DEVELOPMENT = 2;

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
                $this->projectPortalSavedConfig['portal_project_name'] = $projects['portal_project_name'] ?? '';
                $this->projectPortalSavedConfig['portal_project_description'] = $projects['portal_project_description'] ?? '';
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

    public function getProjectMembers()
    {
        try {
            $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
            if(!$r2p2ProjectId){
                throw new \Exception('R2P2 project ID is not provided');
            }
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/projects/' . $r2p2ProjectId . '/users/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                $r2p2Users = json_decode($response->getBody(), true);;
                return $this->matchREDCapUsersWithR2P2($r2p2Users['results']);
            } else {
                throw new \Exception("could not get REDCap signed auth from portal.");
            }
        } catch (\Exception $e) {
            return array();
        }
    }

    public function matchREDCapUsersWithR2P2($r2p2Users)
    {
        $redcapUsers = \REDCap::getUsers();
        $found = false;
        $result = [];
        foreach ($redcapUsers as $user) {
            $object = ExternalModules::getUserInfo($user);
            foreach ($r2p2Users as $r2p2User) {
                if ($user == $r2p2User['user']['username']) {
                    $r2p2User['group'] = $this->findR2P2Group($r2p2User['group_id']);


                    $result[] = array(
                        'redcap' => $object['user_firstname'] . ' ' . $object['user_lastname'] . "($user)",
                        'r2p2' => $r2p2User['user']['first_name'] . ' ' . $r2p2User['user']['last_name'] . "(" . $r2p2User['user']['username'] . ")",
                        'group_id' => $r2p2User['group']['id'],
                        'redcap_username' => $user,
                        'r2p2_user_id' => $r2p2User['user']['id'],
                        'current_user' => $user == USERID
                    );
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $found = false;
                continue;
            }
            $result[] = array(
                'redcap' => $object['user_firstname'] . ' ' . $object['user_lastname'] . "($user)",
                'r2p2' => "N/A",
                'group_id' => '',
                'redcap_username' => $user,
                'r2p2_user_id' => '',
            );;
        }
        return $result;
    }

    public function findR2P2Group($groupId)
    {
        if (!$this->groups) {
            $this->getR2P2Groups();
        }
        foreach ($this->groups['results'] as $group) {
            if ($group['id'] == $groupId) {
                return $group;
            }
        }
    }

    public function getR2P2Groups()
    {
        $jwt = $this->getClient()->getJwtToken();
        $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/groups/', [
            'debug' => false,
            'headers' => [
                'Authorization' => "Bearer {$jwt}",
            ]
        ]);
        if ($response->getStatusCode() < 300) {
            $this->groups = json_decode($response->getBody(), true);
            return $this->groups;
        } else {
            throw new \Exception("could not get REDCap signed auth from portal.");
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

    public function getProjectFinancesRecords()
    {
        if (!$this->projectPortalSavedConfig['portal_project_id']) {
            throw new \Exception('No Linked R2P2 Project found');
        }
        $jwt = $this->getClient()->getJwtToken();
        $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/projects/' . $this->projectPortalSavedConfig['portal_project_id'] . '/finances/', [
            'debug' => false,
            'headers' => [
                'Authorization' => "Bearer {$jwt}",
            ]
        ]);
        if ($response->getStatusCode() < 300) {
            $result = json_decode($response->getBody(), true);
            $result['finances'][] = self::NEW_PTA;

            return $result['finances'];
        } else {
            throw new \Exception("could not pull finances records for R2P2 pid " . $this->projectPortalSavedConfig['portal_project_id']);
        }
    }

    /**
     * @param int $portalProjectId
     * @param int $redcapProjectId
     * @param int $redcapStatus
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchIRB($irbNum)
    {
        try {
            if (!$irbNum) {
                return [];
            }
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/irb/search/', [
                'debug' => false,
                'form_params' => [
                    'number' => $irbNum,
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


    public function addProjectUser($projectId, $user)
    {
        try {
            if (!$user) {
                return [];
            }
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . "api/projects/$projectId/add-user/", [
                'debug' => false,
                'form_params' => [
                    'username' => $user['username'],
                    'email' => $user['user_email'],
                    'first_name' => $user['user_firstname'],
                    'last_name' => $user['user_lastname'],
                    // TODO PULL GROUP DATA FROM R2P2
                    'group_id' => self::ADMIN_GROUP_ID
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

    public function createProject($project)
    {

        if (!$project) {
            return [];
        }
        $jwt = $this->getClient()->getJwtToken();
        $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/', [
            'debug' => false,
            'form_params' => $project,
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

    public function approveSOW($approval, $sowId)
    {

        if (!$approval) {
            return [];
        }
        // manually set status
        $approval['status'] = self::APPROVED_PENDING_DEVELOPMENT;
        $jwt = $this->getClient()->getJwtToken();
        $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
        if (!$r2p2ProjectId) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
        $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . "api/projects/$r2p2ProjectId/sow/$sowId/approve/", [
            'debug' => false,
            'form_params' => $approval,
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

    public function getR2P2ProjectSOWs()
    {
        $jwt = $this->getClient()->getJwtToken();
        $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
        if (!$r2p2ProjectId) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
        $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . "api/projects/$r2p2ProjectId/sow/", [
            'debug' => false,
            'headers' => [
                'Authorization' => "Bearer {$jwt}",
            ]
        ]);
        if ($response->getStatusCode() < 300) {
            $sows = json_decode($response->getBody(), true);
            $result = [];
            foreach ($sows as $sow) {
                if (strpos($sow['title'], 'Sprint Block') !== false) {
                    $sow['reviewed_by'] = $sow['user']['first_name'] . ' ' . $sow['user']['last_name'];
                    $sow['status'] = $this->getSOWStatusText($sow['status']);

                    if (isset($sow['charge_item_sow_id__charge_id__count']) and $sow['charge_item_sow_id__charge_id__count'] >= 1) {
                        $sow['charges'] = $this->getSOWCharges($sow['id']);
                        $sow['amount'] = '$' . $sow['charges'][0]['amount'];
                    }
                    $result[] = $sow;
                }
            }
            return $result;
        } else {
            throw new \Exception("could not get REDCap signed auth from portal.");
        }
    }

    public function getSOWStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'To Be Done';
            case 1:
                return 'Pending';
            case 2:
                return 'Approved Pending Development';
            case 3:
                return 'Revision Requested';
            case 4:
                return 'Denied';
            case 5:
                return 'Done';
            case 6:
                return 'Approved Active Development';
            case 7:
                return 'Approved Maintenance';
            case 8:
                return 'Internal Review';
        }
    }

    public function getSOWCharges($sowId)
    {
        $jwt = $this->getClient()->getJwtToken();
        $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
        if (!$r2p2ProjectId) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
        $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . "api/projects/sow/$sowId/charges/", [
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

    public function createNewPTA($finance)
    {

        if (!$finance) {
            return [];
        }
        $jwt = $this->getClient()->getJwtToken();
        $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
        if (!$r2p2ProjectId) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
        $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . "api/projects/$r2p2ProjectId/add-finance/", [
            'debug' => false,
            'form_params' => $finance,
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

    public function addUserToR2P2Project($user)
    {

        if (!$user) {
            return [];
        }
        $jwt = $this->getClient()->getJwtToken();
        $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
        if (!$r2p2ProjectId) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
        $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . "api/projects/$r2p2ProjectId/add-user/", [
            'debug' => false,
            'form_params' => $user,
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

    public function updateUserToR2P2Project($user, $r2p2UserId)
    {

        if (!$user) {
            return [];
        }
        $jwt = $this->getClient()->getJwtToken();
        $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
        if (!$r2p2ProjectId) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
        $response = $this->getClient()->getGuzzleClient()->put($this->getClient()->getPortalBaseURL() . "api/projects/$r2p2ProjectId/update-user/$r2p2UserId/", [
            'debug' => false,
            'form_params' => $user,
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

    public function deleteUserFromR2P2Project($r2p2UserId)
    {
        $jwt = $this->getClient()->getJwtToken();
        $r2p2ProjectId = $this->projectPortalSavedConfig['portal_project_id'];
        if (!$r2p2ProjectId) {
            throw new \Exception('This project is not Linked to R2P2 project.');
        }
        $response = $this->getClient()->getGuzzleClient()->delete($this->getClient()->getPortalBaseURL() . "api/projects/$r2p2ProjectId/delete-user/$r2p2UserId/", [
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

    public function searchUsers($term)
    {
        try {
            if (!$term) {
                return [];
            }
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/users/search/?term=' . $term, [
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
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getR2P2UserProjects($suid)
    {
        try {
            if (!$suid) {
                return [];
            }
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/users/' . $suid . '/projects/', [
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
    public function requstProjectAccess($portalProjectId, $user)
    {

        $jwt = $this->getClient()->getJwtToken();
        $response = $this->getClient()->getGuzzleClient()->post($this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/request-access/', [
            'debug' => false,
            'form_params' => [
                'on_behalf_of_email' => $user['on_behalf_of_email'],
                'on_behalf_of_username' => $user['on_behalf_of_username'],
                'on_behalf_of_first_name' => $user['on_behalf_of_first_name'],
                'on_behalf_of_last_name' => $user['on_behalf_of_last_name'],
            ],
            'headers' => [
                'Authorization' => "Bearer {$jwt}",
            ]
        ]);
        if ($response->getStatusCode() < 300) {
            return json_decode($response->getBody(), true);
        } else {
            throw new \Exception("could not get request access to R2P2 pid $portalProjectId");
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
    public function searchServiceBlock($id, $fundingSource)
    {
        $blocks = $this->sprintBlocks;
        // if funding source is not industrial
        if($fundingSource != 3){
            $blocks = $this->discountedSprintBlocks;
        }
        foreach ($blocks as $sprintBlock) {
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
                    'charges' => json_encode($charges),
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