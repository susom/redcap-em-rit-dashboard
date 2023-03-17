<?php


namespace Stanford\ProjectPortal;

use REDCap;

/**
 * Class User
 * @package Stanford\ProjectPortal
 * @property \Stanford\ProjectPortal\Client $client
 * @property \Stanford\ProjectPortal\Entity $entity
 * @property array $userJiraTickets
 * @property array $projectPortalList
 * @property int $projectId
 */
class User
{
    private $userJiraTickets;

    private $client;

    private $projectId;

    private $projectPortalList;

    private $entity;


    /**
     * @var array
     */
    private $redcapUsers = [];

    /**
     * @param $client
     * @param \Stanford\ProjectPortal\Entity $entity
     * @param int|null $projectId
     */
    public function __construct($client, $entity, $projectId = null)
    {
        $this->setClient($client);

        $this->setProjectId($projectId);

        $this->setEntity($entity);
    }


    public function processUserRequest($users, $excludedProject)
    {
        $result = array();
        foreach ($users as $user) {
            $sql = "SELECT rr.project_id as project_id, rrc.record_count, rp.app_title as project_name, rp.status, rp.last_logged_event, rp.project_note as description FROM redcap_user_information ri JOIN redcap_user_rights rr ON ri.username = rr.username INNER JOIN redcap_projects rp on rr.project_id = rp.project_id INNER JOIN redcap_record_counts rrc on rp.project_id = rrc.project_id WHERE ri.username = '$user' OR ri.user_email LIKE '%$user%' OR ri.user_firstname LIKE '%$user%' OR ri.user_lastname LIKE '%$user%'";
            $q = db_query($sql);

            $sql_em = "SELECT project_id, SUM(maintenance_fees) as total from redcap_entity_project_external_modules_usage GROUP BY project_id";
            $q_em = db_query($sql_em);
            $ems = [];
            if (db_num_rows($q_em) > 0) {
                while ($row_em = db_fetch_assoc($q_em)) {
                    $ems[$row_em['project_id']] = $row_em['total'];
                }
            }
            if (db_num_rows($q) > 0) {
                while ($row = db_fetch_assoc($q)) {
                    // exclude redcap projects that are linked to different research project different from the one making this call
                    if (!empty($excludedProject) && in_array($row['project_id'], $excludedProject)) {
                        continue;
                    }
                    $projectId = $row['project_id'];
                    $sql = "SELECT ri.username, ri.user_email, ri.user_firstname, ri.user_lastname FROM redcap_user_rights rr RIGHT JOIN redcap_user_information ri ON rr.username = ri.username WHERE rr.project_id = '$projectId' AND rr.username != '$user'";
                    $records = db_query($sql);
//                    if (db_num_rows($records) > 0) {
                    // init project object to get basic information about the project


                    $temp = array(
                        'project_name' => $row['project_name'],
                        'project_id' => $row['project_id'],
                        'project_status' => $row['status'] ? 'Production' : 'Development',
                        'last_logged_event' => $row['last_logged_event'],
                        'record_count' => $row['record_count'],
                        //'maintenance_fees' => $this->getEntity()->getProjectTotalMaintenanceFees($row['project_id']),
                        'maintenance_fees' => $ems[$row['project_id']],
                    );
                    // get project users other the main one we sent via API
                    if (db_num_rows($records) > 0) {
                        while ($record = db_fetch_assoc($records)) {
                            $temp['users'][] = $record;
                        }
                    }
                    $result[$user][] = $temp;
                    unset($temp);
//                    }
                }
            }

        }
        header("Content-type: application/json");
        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserJiraTickets(): array
    {
        if (!$this->userJiraTickets) {
            $this->setUserJiraTickets();
        }
        return $this->userJiraTickets;
    }

    public function generateCustomSurveyRecord($projectId, $instrument, $data = array())
    {
        $reservedRecordId = REDCap::reserveNewRecordId($this->getProjectId());
        $data[REDCap::getRecordIdField()] = $reservedRecordId;
        $response = \REDCap::saveData($projectId, 'json', json_encode(array($data)));
        if (empty($response['errors'])) {
            $url = REDCap::getSurveyLink($reservedRecordId, $instrument);

            $parts = explode("s=", $url);
            $hash = end($parts);
            if (isset($data['portal_redirect_url'])) {
                $data['portal_redirect_url'] = $data['portal_redirect_url'] . '?svh=' . $hash;
                \REDCap::saveData($projectId, 'json', json_encode(array($data)));
            }

            return array('status' => 'success', 'record_id' => $reservedRecordId, 'url' => $url);
        } else {
            throw new \LogicException("cant create new record");
        }

    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setUserJiraTickets(): void
    {
        try {
            if (!defined('USERID')) {
                throw new \Exception("no user defined");
            }
            //$this->getProjectPortalJWTToken();
            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/issues/' . USERID . '/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                $data = json_decode($response->getBody());
                $this->userJiraTickets = json_decode(json_encode($data), true);
            }

        } catch (\Exception $e) {
            $this->userJiraTickets = [];
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
     * @return array
     */
    public function getProjectPortalList()
    {
        if (!$this->projectPortalList) {
            $this->setProjectPortalList();
        }
        return $this->projectPortalList;
    }

    /**
     * @param array $projectPortalList
     */
    public function setProjectPortalList()
    {
        try {
            # get or update current jwt token to make requests to project portal api
            //$this->getProjectPortalJWTToken();

            if (defined('USERID')) {
                $client = $this->getClient()->getGuzzleClient();
                $jwt = $this->getClient()->getJwtToken();
                $response = $client->get($this->getClient()->getPortalBaseURL() . 'api/users/' . USERID . '/projects/', [
                    'debug' => false,
                    'headers' => [
                        'Authorization' => "Bearer {$jwt}",
                    ]
                ]);
                if ($response->getStatusCode() < 300) {
                    $data = json_decode($response->getBody());
                    $this->projectPortalList = json_decode(json_encode($data), true);
                    // append new item for create new projects
                    $this->projectPortalList[] = array('id' => 'new', 'project_name' => '**Create New Project**');
                }
            }
        } catch (\Exception $e) {
            $this->projectPortalList = [];
        }

    }

    /**
     * @return bool
     */
    public function isUserHasManagePermission(): bool
    {
        if (defined('PROJECT_ID') and (!defined('NOAUTH') || NOAUTH == false)) {

            //this function return right for main user when hit it with survey respondent!!!!!
            $right = REDCap::getUserRights();
            $user = end($right);
            if ($user['design'] === "1") {
                return true;
            }
        } elseif (defined('SUPER_USER') && SUPER_USER == "1") {
            return true;
        }

        return false;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return array
     */
    public function getRedcapUsers($redcapProjectId = ''): array
    {
        if (!$this->redcapUsers) {
            $this->setRedcapUsers($redcapProjectId);
        }
        return $this->redcapUsers;
    }

    /**
     * @return void
     */
    public function setRedcapUsers($redcapProjectId): void
    {
        if ($redcapProjectId != '') {
            $sql = "SELECT username, CONCAT(user_firstname, ' ', user_lastname) as `full_name` FROM redcap_user_information where username in (select username from redcap_user_rights where project_id = $redcapProjectId)";
        } else {
            $sql = "SELECT username, CONCAT(user_firstname, ' ', user_lastname) as `full_name` FROM redcap_user_information";
        }
        $q = db_query($sql);
        $result = array();
        if (db_num_rows($q) > 0) {
            while ($row = db_fetch_assoc($q)) {
                $result[] = array('username' => $row['username'], 'full_name' => $row['full_name']);
            }
        }
        $this->redcapUsers = json_decode(json_encode($result), true);
    }


}