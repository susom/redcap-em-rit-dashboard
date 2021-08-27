<?php


namespace Stanford\ProjectPortal;

use REDCap;
/**
 * Class User
 * @package Stanford\ProjectPortal
 * @property \Stanford\ProjectPortal\Client $client
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

    /**
     * User constructor.
     * @param \Stanford\ProjectPortal\Client $client
     */
    public function __construct($client, $projectId = null)
    {
        $this->setClient($client);

        $this->setProjectId($projectId);
    }


    public function processUserRequest($users, $excludedProject)
    {
        $result = array();
        foreach ($users as $user) {
            $sql = "SELECT project_id FROM redcap_user_information ri JOIN redcap_user_rights rr ON ri.username = rr.username WHERE ri.username = '$user' OR ri.user_email LIKE '%$user%' OR ri.user_firstname LIKE '%$user%' OR ri.user_lastname LIKE '%$user%'";
            $q = db_query($sql);
            if (db_num_rows($q) > 0) {
                while ($row = db_fetch_assoc($q)) {
                    // exclude redcap projects that are linked to different research project different from the one making this call
                    if (!empty($excludedProject) && in_array($row['project_id'], $excludedProject)) {
                        continue;
                    }
                    $projectId = $row['project_id'];
                    $sql = "SELECT ri.username, ri.user_email, ri.user_firstname, ri.user_lastname FROM redcap_user_rights rr RIGHT JOIN redcap_user_information ri ON rr.username = ri.username WHERE rr.project_id = '$projectId' AND rr.username != '$user'";
                    $records = db_query($sql);
                    if (db_num_rows($records) > 0) {
                        // init project object to get basic information about the project
                        $project = new \Project($projectId);

                        $temp = array(
                            'project_name' => $project->project['app_title'],
                            'project_id' => $project->project_id,
                            'project_status' => $project->project['status'] ? 'Production' : 'Development',
                            'last_logged_event' => $project->project['last_logged_event'],
                            'record_count' => \Records::getRecordCount($project->project_id),
                        );
                        // get project users other the main one we sent via API
                        while ($record = db_fetch_assoc($records)) {
                            $temp['users'][] = $record;
                        }
                        $result[$user][] = $temp;
                        unset($temp);
                    }
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
                }
            }
        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
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
}