<?php

namespace Stanford\ProjectPortal;

require_once("emLoggerTrait.php");


use ExternalModules\ExternalModules;
use REDCap;
use Records;
use ExternalModules\AbstractExternalModule;
use Sabre\DAV\Exception;

/**
 * Class ProjectPortal
 * @package Stanford\ProjectPortal
 * @property string $token
 * @property string $user
 * @property array $ips
 * @property array $projects
 * @property \Project $project
 * @property string $request
 * @property string $jwtToken
 * @property string $portalUsername
 * @property string $portalPassword
 * @property string $portalBaseURL
 * @property array $projectPortalSavedConfig
 * @property array $projectPortalList
 */
class ProjectPortal extends AbstractExternalModule
{
    use emLoggerTrait;

    /**
     * @var
     */
    private $token;


    private $jwtToken;
    /**
     * @var
     */
    private $user;

    /**
     * @var array of whitelisted ips
     */
    private $ips;

    /**
     * @var array of enabled projects
     */
    private $projects;

    /**
     * @var \Project
     */
    private $project;
    /**
     * @var
     */
    private $request;

    /**
     * @var
     */
    public $projectPortalSavedConfig;

    /**
     * @var
     */
    private $projectPortalList;

    private $portalUsername;

    private $portalPassword;

    private $portalBaseURL;

    /**
     * ProjectPortal constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setToken($this->getSystemSetting('project-portal-api-token'));

        $this->setPortalUsername($this->getSystemSetting('portal-username'));

        $this->setPortalPassword($this->getSystemSetting('portal-password'));

        $this->setPortalBaseURL($this->getSystemSetting('portal-base-url'));

        if ($_GET && ($_GET['projectid'] != null || $_GET['pid'] != null)) {

            $this->setProjects($this->getEnabledProjects());

            // set these fields as we might need them later for linkage process.
            $this->setProjectPortalSavedConfig();
        }
    }

    /**
     * call django endpoint to attach redcap project to portal project. need more testnig
     * @param $projectId
     * @throws \Exception
     */
    public function attachToProjectPortal($portalProjectId, $portalProjectName, $portalProjectDescription)
    {
        try {
            $this->setProject(new \Project($this->getProjectId()));
            $this->getProjectPortalJWTToken();

            $client = new \GuzzleHttp\Client();
            $jwt = $this->getJwtToken();
            $this->setProject(new \Project($this->getProjectId()));
            $response = $client->post($this->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/attach-redcap/', [
                'debug' => false,
                'form_params' => [
                    'redcap_project_id' => $this->getProjectId(),
                    'redcap_project_name' => $this->getProject()->project['app_title'],
                ],
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                $data = json_decode($response->getBody());
                $this->setProjectPortalList(json_decode(json_encode($data), true));
            }

            $inputs = array(
                'portal_project_id' => $portalProjectId,
                'portal_project_name' => $portalProjectName,
                'portal_project_description' => $portalProjectDescription,
                'portal_project_url' => $this->getPortalBaseURL() . 'detail/' . $portalProjectId,
            );
            $this->savePortalProjectInfoInREDCap($inputs);
        } catch (\Exception $e) {
           throw new \LogicException($e->getMessage());
        }
    }

    private function savePortalProjectInfoInREDCap($inputs)
    {
        try {

            if (!empty($inputs) && $inputs['portal_project_id'] != '') {
                #$projects = $this->getSystemSetting('linked-portal-projects');
                $projects = $this->getProjectSetting('linked-project', $this->getProject()->project_id);
                $projects = json_decode($projects, true);
                $projects[$this->getProject()->project_id] = $inputs;

                $settings = json_encode($projects);
            } else {
                $settings = '';
            }

            ExternalModules::saveSettings($this->PREFIX, $this->getProject()->project_id, array('linked-project' => $settings));
        } catch (\Exception $e) {

        }
    }

    public function isREDCapProjectLinkedToProjectPortalProject()
    {
        return isset($this->projectPortalSavedConfig['portal_project_id']) && $this->projectPortalSavedConfig['portal_project_id'] != '';
    }

    public function getProjectPortalSavedConfig()
    {
        return $this->projectPortalSavedConfig;
    }


    public function setProjectPortalSavedConfig()
    {
        #$projects = $this->getSystemSetting('linked-portal-projects');
        $projects = $this->getProjectSetting('linked-project');
        if (!empty($projects)) {
            $projects = json_decode($projects, true);
            if (isset($projects[$this->getProjectId()])) {
                $project = $projects[$this->getProjectId()];
                $this->projectPortalSavedConfig['portal_project_id'] = $project['portal_project_id'];
                $this->projectPortalSavedConfig['portal_project_name'] = $project['portal_project_name'];
                $this->projectPortalSavedConfig['portal_project_description'] = $project['portal_project_description'];
                $this->projectPortalSavedConfig['portal_project_url'] = $project['portal_project_url'];
            }
        }

    }

    public function prepareProjectPortalList()
    {
        try {
            # get or update current jwt token to make requests to project portal api
            $this->getProjectPortalJWTToken();

            $client = new \GuzzleHttp\Client();
            $jwt = $this->getJwtToken();
            $response = $client->get($this->getPortalBaseURL() . 'api/users/' . USERID . '/projects/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                $data = json_decode($response->getBody());
                $this->setProjectPortalList(json_decode(json_encode($data), true));
            }
        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
        }
    }

    public function redcap_every_page_top()
    {
        // in case we are loading record homepage load its the record children if existed
        if (strpos($_SERVER['SCRIPT_NAME'], 'ProjectSetup') !== false) {
            $this->includeFile("views/project_setup.php");

        }

        // this to override the functionality for contact admin button on all pages.
        $this->includeFile("views/contact_admin_button.php");
    }

    /**
     * check is jwt token is still valid current expiration time is 2 days
     * @return bool
     */
    private function isJWTTokenStillValid()
    {
        if (isset($_SESSION['project_portal_jwt_token_created_at']) && (time() - $_SESSION['project_portal_jwt_token_created_at'] < 60 * 60 * 24 * 2)) {
            return true;
        } else {
            return false;
        }
    }

    private function getProjectPortalJWTToken()
    {
        try {
            if (isset($_SESSION['project_portal_jwt_token']) && $this->isJWTTokenStillValid()) {
                $this->setJwtToken($_SESSION['project_portal_jwt_token']);
            } else {
                $client = new \GuzzleHttp\Client();

                $response = $client->post($this->getPortalBaseURL() . 'api/users/token/', [
                    'debug' => false,
                    'form_params' => [
                        'username' => $this->getPortalUsername(),
                        'password' => $this->getPortalPassword(),
                    ],
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ]
                ]);
                if ($response->getStatusCode() < 300) {
                    $data = json_decode($response->getBody());
                    $this->setJwtToken($data->token);
                    $this->setJWTTokenIntoSession($data->token);
                }
            }
        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
        }

    }
    public function processRequest()
    {
        if (!isset($_POST) OR empty($_POST)) {
            throw new \LogicException("you are not allowed to access the API");
        }

        if (!isset($_POST['secret_token']) || !isset($_POST['request'])) {
            throw new \LogicException("request parameter is missing");
        }

        if (!$this->applyIpFilter()) {
            throw new \LogicException("you are not allowed to access the API");
        }

        $token = filter_var($_POST['secret_token'], FILTER_SANITIZE_STRING);
        if (!$this->verifyToken($token)) {
            throw new \LogicException("wrong token provided");
        }

        // set request
        $this->setRequest(filter_var($_POST['request'], FILTER_SANITIZE_STRING));

        if ($this->getRequest() == 'users') {
            if (!isset($_POST['users'])) {
                throw new \LogicException("request parameter is missing");
            }
            $users = explode(',', filter_var($_POST['users'], FILTER_SANITIZE_STRING));
            if (empty($users)) {
                throw new \LogicException("no users were passed");
            }
            $this->processUserRequest($users);
        } elseif ($this->getRequest() == "add_project") {
            if (!isset($_POST['redcap_project_id'])) {
                throw new \LogicException("REDCap project id parameter is missing");
            }
            if (!isset($_POST['portal_project_id'])) {
                throw new \LogicException("project id parameter is missing");
            }
            if (!isset($_POST['portal_project_name'])) {
                throw new \LogicException("project name parameter is missing");
            }
            if (!isset($_POST['portal_project_description'])) {
                throw new \LogicException("project description parameter is missing");
            }
            if (!isset($_POST['portal_project_url'])) {
                throw new \LogicException("project url parameter is missing");
            }

            $args = array(
                'redcap_project_id' => FILTER_SANITIZE_NUMBER_INT,
                'portal_project_id' => FILTER_SANITIZE_NUMBER_INT,
                'portal_project_name' => FILTER_SANITIZE_STRING,
                'portal_project_description' => FILTER_SANITIZE_STRING,
                'portal_project_url' => FILTER_SANITIZE_STRING,
            );

            $inputs = filter_var_array($_POST, $args);
            $this->processAddProjectRequest($inputs);
            return 'Portal Project is not Linked to a REDCap project';
        } elseif ($this->getRequest() == "remove_project") {
            if (!isset($_POST['redcap_project_id'])) {
                throw new \LogicException("REDCap project id parameter is missing");
            }
            $this->setProject(new \Project(filter_var($_POST['redcap_project_id'], FILTER_SANITIZE_NUMBER_INT)));
            $this->savePortalProjectInfoInREDCap([]);
            return 'The linkage between Portal Project and REDCap is not removed!';
        }

    }

    private function processAddProjectRequest($inputs)
    {
        try {
            $this->emDebug($inputs);
            $this->setProject(new \Project($inputs['redcap_project_id']));
            $this->savePortalProjectInfoInREDCap($inputs);
            header("Content-type: application/json");
            http_response_code(200);
            echo json_encode(array("Project information has been updated."));
        } catch (\Exception $e) {
            header("Content-type: application/json");
            http_response_code(404);
            echo json_encode(array($e->getMessage()));
        }
    }

    private function processUserRequest($users)
    {
        $result = array();
        foreach ($users as $user) {
            $sql = "SELECT project_id FROM redcap_user_information ri JOIN redcap_user_rights rr ON ri.username = rr.username WHERE ri.username = '$user' OR ri.user_email LIKE '%$user%' OR ri.user_firstname LIKE '%$user%' OR ri.user_lastname LIKE '%$user%'";
            $q = db_query($sql);
            if (db_num_rows($q) > 0) {
                while ($row = db_fetch_assoc($q)) {
                    $projectId = $row['project_id'];
                    $sql = "SELECT ri.username, ri.user_email, ri.user_firstname, ri.user_lastname FROM redcap_user_rights rr RIGHT JOIN redcap_user_information ri ON rr.username = ri.username WHERE rr.project_id = '$projectId' AND rr.username != '$user'";
                    $records = db_query($sql);
                    if (db_num_rows($records) > 0) {
                        // init project object to get basic information about the project
                        $project = new \Project($projectId);

                        $temp = array(
                            'project_name' => $project->project['app_title'],
                            'project_id' => $project->project_id,
                            'last_logged_event' => $project->project['last_logged_event'],
                            'record_count' => Records::getRecordCount($project->project_id),
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
     * Apply the IP filter if set
     */
    private function applyIpFilter()
    {

        $ip_addr = trim($_SERVER['REMOTE_ADDR']);
        $this->emDebug("Project Portal Report API - Incoming IP address: " . $ip_addr);

        // APPLY IP FILTER
        $ip_filter = $this->getSystemSetting('ip');
        if (!empty($ip_filter) && !empty($ip_filter[0]) && empty($_POST['magic_skip_cidr'])) {
            $isValid = false;
            foreach ($ip_filter as $filter) {
                if (self::ipCIDRCheck($filter, $ip_addr)) {
                    $isValid = true;
                    break;
                }
            }
            // Exit - invalid IP
            if (!$isValid) {

                // Send email to designated user if IP is invalid
                $emailTo = $this->getSystemSetting('alert-email');
                if (!empty($emailTo)) {
                    $emailFrom = "noreply@stanford.edu";
                    $subject = "Unauthorized IP trying to access Biocatalyst Reports";
                    $body = "IP address $ip_addr is trying to access Biocatalyst Reports and is not in the approved IP range.";
                    $status = REDCap::email($emailTo, $emailFrom, $subject, $body);
                }

                // Return error
                $this->emError($subject, $body);
                $this->returnError("Invalid source IP. Please contact REDCap Administrator to whitelist this IP:" . $ip_addr);
            }
        }
        return true;
    }

    /**
     * check if provided token is correct or not
     * @param $token
     * @return bool
     */
    private function verifyToken($token)
    {
        return $token == $this->getToken();
    }


    /**
     * Return an error message and exit
     * @param string $error_message
     * @param int $http_code
     */
    private function returnError($error_message, $http_code = 404)
    {
        header("Content-type: application/json");
        http_response_code($http_code);
        echo json_encode(["error" => $error_message]);

        $this->emError($error_message);
        exit();
    }

    /**
     * Return array of projects with all project metadata where biocatalyst is enabled
     * regardless of user permissions
     *
     * @return array
     */
    private function getEnabledProjects()
    {
        $projects = array();
        $sql = "select rp.project_id, rp.app_title
          from redcap_external_modules rem
          left join redcap_external_module_settings rems on rem.external_module_id = rems.external_module_id
          left join redcap_projects rp on rems.project_id = rp.project_id
          where rem.directory_prefix = 'biocatalyst_link'
          and rems.key = 'biocatalyst-enabled'
          and rems.value = 'true'";
        $q = $this->query($sql);
        while ($row = db_fetch_assoc($q)) {
            $projects[] = $row;
        }

        return $projects;
    }


    /**
     * Utility function to verify IP is from valid range if specified
     *
     * e.g. 192.168.123.1 = 192.168.123.1/30
     * @param $CIDR
     * @return bool
     */
    private static function ipCIDRCheck($CIDR, $ip)
    {


        // Convert IPV6 localhost into IPV4
        if ($ip == "::1") {
            $ip = "127.0.0.1";
        }

        if (strpos($CIDR, "/") === false) {
            $CIDR .= "/32";
        }
        list ($net, $mask) = explode("/", $CIDR);
        $ip_net = ip2long($net);
        $ip_mask = ~((1 << (32 - $mask)) - 1);
        $ip_ip = ip2long($ip);
        $ip_ip_net = $ip_ip & $ip_mask;
        return ($ip_ip_net == $ip_net);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getIps()
    {
        return $this->ips;
    }

    /**
     * @param array $ips
     */
    public function setIps($ips)
    {
        $this->ips = $ips;
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param array $projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param string $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getJwtToken()
    {
        return $this->jwtToken;
    }

    /**
     * @param string $jwtToken
     */
    public function setJwtToken($jwtToken)
    {
        $this->jwtToken = $jwtToken;
    }

    private function setJWTTokenIntoSession($jwtToken)
    {
        $_SESSION['project_portal_jwt_token'] = $jwtToken;
        $_SESSION['project_portal_jwt_token_created_at'] = time();
    }

    /**
     * @param string $path
     */
    public function includeFile($path)
    {
        include_once $path;
    }

    /**
     * @return array
     */
    public function getProjectPortalList()
    {
        return $this->projectPortalList;
    }

    /**
     * @param array $projectPortalList
     */
    public function setProjectPortalList($projectPortalList)
    {
        $this->projectPortalList = $projectPortalList;
    }

    /**
     * @return string
     */
    public function getPortalUsername()
    {
        return $this->portalUsername;
    }

    /**
     * @param string $redcapUsername
     */
    public function setPortalUsername($redcapUsername)
    {
        $this->portalUsername = $redcapUsername;
    }

    /**
     * @return string
     */
    public function getPortalPassword()
    {
        return $this->portalPassword;
    }

    /**
     * @param string $redcapPassword
     */
    public function setPortalPassword($redcapPassword)
    {
        $this->portalPassword = $redcapPassword;
    }

    /**
     * @return string
     */
    public function getPortalBaseURL()
    {
        return $this->portalBaseURL;
    }

    /**
     * @param string $portalBaseURL
     */
    public function setPortalBaseURL($portalBaseURL)
    {
        $this->portalBaseURL = $portalBaseURL;
    }


}