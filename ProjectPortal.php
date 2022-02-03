<?php

namespace Stanford\ProjectPortal;

require __DIR__ . '/vendor/autoload.php';
require_once("emLoggerTrait.php");
require_once("classes/User.php");
require_once("classes/Support.php");
require_once("classes/Client.php");
require_once("classes/Portal.php");
require_once("classes/Entity.php");
require_once("classes/ManagerEM.php");
require_once("classes/Utilities.php");

const MAJOR_VERSION = 7 ;
use ExternalModules\ExternalModules;
use REDCap;
use Records;
use ExternalModules\AbstractExternalModule;
use Sabre\DAV\Exception;
use Stanford\ProjectPortal\Client;
use Stanford\ProjectPortal\Support;
use Stanford\ProjectPortal\User;
use Stanford\ProjectPortal\ManagerEM;

/**
 * Class ProjectPortal
 * @package Stanford\ProjectPortal
 * @property \Stanford\ProjectPortal\User $user
 * @property \Stanford\ProjectPortal\Support $support
 * @property \Stanford\ProjectPortal\Client $client
 * @property \Stanford\ProjectPortal\Portal $portal
 * @property \Stanford\ProjectPortal\Entity $entity
 * @property \Stanford\ProjectPortal\ManagerEM $managerEm
 * @property array $ips
 * @property array $projects
 * @property \Project $project
 * @property string $request
 * @property array $projectPortalList
 * @property array $jiraIssueTypes
 * @property array $userJiraTickets
 * @property array $notifications
 */
class ProjectPortal extends AbstractExternalModule
{
    use emLoggerTrait;

    /**
     * @var
     */
    private $user;

    private $support;

    private $client;

    private $portal;
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


    private $managerEm;

    /**
     * @var
     */
    private $projectPortalList;


    private $jiraIssueTypes;

    private $userJiraTickets;

    private $notifications;

    /**
     * determine what is project state
     * @var int
     */
    private $state;

    /**
     * ProjectPortal constructor.
     */
    public function __construct()
    {
        parent::__construct();


        $this->setClient(new Client($this->PREFIX));


        $this->setManagerEm(new \Stanford\ProjectPortal\ManagerEM($this->getClient(), $this->PREFIX));


        $this->setEntity(new Entity());

        if ($_GET && ($_GET['projectid'] != null || $_GET['pid'] != null)) {

            $this->setProject(new \Project($this->getProjectId()));

            //$this->setProjects($this->getEnabledProjects());

            $this->setPortal(new Portal($this->getClient(), $this->getProjectId(), $this->getProject()->project['app_title'], $this->getProject()->project['status']));

            // only make connection if and only if the called page within this EM
            preg_match('/prefix=rit_dashboard.*page=.*/m', $_SERVER['REQUEST_URI'], $matches, PREG_OFFSET_CAPTURE);
            if (!empty($matches)) {
                $this->getPortal()->prepareR2P2SavedProject();
            }

        }


        // set these fields as we might need them later for linkage process.

        $this->setUser(new User($this->getClient(), $this->getEntity(), $this->getProjectId()));

        $this->setSupport(new Support($this->getClient()));


    }

    public function checkProjectsRMACron()
    {
        $projects = self::query("select project_id, app_title from redcap_projects where status = 1", []);

        while ($project = $projects->fetch_assoc()) {
            $id = $project['project_id'];
            $url = $this->getUrl("ajax/entity/cron.php", true) . '&pid=' . $id;
            $this->getClient()->getGuzzleClient()->request('GET', $url, array(\GuzzleHttp\RequestOptions::SYNCHRONOUS => true));
            $this->emDebug("running cron for $url on project " . $project['app_title']);
        }
    }

    // override code function to allow any logged in user to access the dashboard.
    public function redcap_module_link_check_display($project_id, $link)
    {
        if (ExternalModules::isNoAuth()) {
            // Anyone can view NOAUTH pages.
            // Remember, redcap_module_link_check_display() is currently only called for pages defined in config.json.
            return $link;
        }

        if (ExternalModules::isSuperUser()) {
            // Super users can see all pages
            return $link;
        }

        $username = ExternalModules::getUsername();
        if (!empty($project_id) && $username) {
            return $link;
        }

        return null;
    }

    /**
     * this to save input to the project EM settings
     * @param array $inputs
     */
    public function savePortalProjectInfoInREDCap($inputs)
    {
        try {

            //if (!empty($inputs) && $inputs['portal_project_id'] != '') {
            #$projects = $this->getSystemSetting('linked-portal-projects');
            $projects = $this->getProjectSetting('linked-project', $this->getProject()->project_id);
            $projects = json_decode($projects, true);
            $projects[$this->getProject()->project_id] = $inputs;

            $settings = json_encode($projects);
//            } else {
//                $settings = '';
//            }

            ExternalModules::saveSettings($this->PREFIX, $this->getProject()->project_id, array('linked-project' => $settings));
            header("Content-type: application/json");
            http_response_code(200);
            echo json_encode(array('status' => 'error', 'message' => "Project information has been updated."));
        } catch (\Exception $e) {

        }
    }


    public function redcap_every_page_top()
    {
        // in case we are loading record homepage load its the record children if existed
        preg_match('/redcap_v[\d\.].*\/index\.php/m', $_SERVER['SCRIPT_NAME'], $matches, PREG_OFFSET_CAPTURE);
        if (strpos($_SERVER['SCRIPT_NAME'], 'ProjectSetup') !== false || !empty($matches)) {
            $this->includeFile("views/project_setup.php");
        }
//        elseif (strpos($_GET['page'], 'create_jira_ticket') !== false) {
//            $this->prepareProjectPortalList();
//
//        }
        // this to override the functionality for contact admin button on all pages.
        $this->includeFile("views/contact_admin_button.php");
    }

    /**
     * @throws \Exception
     */
    public function processRequest()
    {
        if (!isset($_POST) or empty($_POST)) {
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

        if ($this->getRequest() == 'version') {
            header("Content-type: application/json");
            http_response_code(200);
            echo json_encode(array(REDCAP_VERSION));
        } elseif ($this->getRequest() == 'webroot_path') {
            header("Content-type: application/json");
            http_response_code(200);
            echo json_encode(array(APP_PATH_WEBROOT));
        } elseif ($this->getRequest() == 'users') {
            if (!isset($_POST['users'])) {
                throw new \LogicException("request parameter is missing");
            }
            $users = explode(',', filter_var($_POST['users'], FILTER_SANITIZE_STRING));
            if ($_POST['excluded_projects'] != '') {
                $excludedProject = explode(',', filter_var($_POST['excluded_projects'], FILTER_SANITIZE_STRING));
            } else {
                $excludedProject = array();
            }
            if (empty($users)) {
                throw new \LogicException("no users were passed");
            }
            $this->getUser()->processUserRequest($users, $excludedProject);
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
        } elseif ($this->getRequest() == "custom_survey") {
            if (!isset($_POST['instrument'])) {
                throw new \LogicException("REDCap instrument is missing!");
            }
            if (!array_key_exists($_POST['instrument'], $this->getProject()->forms)) {
                throw new \LogicException("REDCap instrument does not exist!");
            }

            $data = array();
            if (isset($_POST['portal_redirect_url'])) {
                $data['portal_redirect_url'] = filter_var($_POST['portal_redirect_url'], FILTER_SANITIZE_URL);
            }
            // use the title from portal if available.
            if (isset($_POST['portal_project_title'])) {
                if ($this->isFieldInInstrument(filter_var($_POST['instrument'], FILTER_SANITIZE_STRING), 'portal_project_title')) {
                    $data['portal_project_title'] = filter_var($_POST['portal_project_title'], FILTER_SANITIZE_STRING);
                }
            }

            if (isset($_POST['portal_webauth_user'])) {
                if ($this->isFieldInInstrument(filter_var($_POST['instrument'], FILTER_SANITIZE_STRING), 'portal_webauth_user')) {
                    $data['portal_webauth_user'] = filter_var($_POST['portal_webauth_user'], FILTER_SANITIZE_STRING);
                }
            }

            echo json_encode($this->getUser()->generateCustomSurveyRecord($this->getProjectId(), filter_var($_POST['instrument'], FILTER_SANITIZE_STRING), $data));
        } elseif ($this->getRequest() == "get_redcap_record") {
            if (!isset($_POST['record_id'])) {
                throw new \LogicException("REDCap Record ID is missing!");
            }
            $result = $this->getEntity()->getREDCapRecordViaID($this->getProjectId(), $this->getFirstEventId(), filter_var($_POST['record_id'], FILTER_SANITIZE_STRING));

            echo json_encode($result[filter_var($_POST['record_id'], FILTER_SANITIZE_STRING)][$this->getFirstEventId()]);
        } elseif ($this->getRequest() == "get_redcap_enabled_ems") {
            if (!isset($_POST['redcap_project_id'])) {
                throw new \LogicException("REDCap Project ID is missing!");
            }
            // make sure to update project utilized EMs to make sure array is up-to-date.
            $this->getManagerEm()->updateProjectEMUtil($this->getProjectId());

            $result = $this->getEntity()->generateProjectEMUsageArray(filter_var($_POST['redcap_project_id'], FILTER_SANITIZE_NUMBER_INT));
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            throw new \Exception("something went wrong!");
        }

    }


    public function processOverduePayments()
    {
        $overdue = $this->getEntity()->getOverduePayments($this->getProjectId());
        $overdueArray = [];
        if (!empty($overdue)) {
            $month = date('m', time());

            foreach ($overdue as $item) {
                // no need to add current month overdue payment.
                if ($month == $item['month']) {
                    continue;
                }
                $overdueArray[] = array(
                    'content' => 'Overdue payment for month of  ' . date("F", strtotime('00-' . $item['month'] . '-01') . '-' . $item['year']),
                    'monthly_payments' => $item['monthly_payments']
                );
            }
            $overdue = json_encode($overdueArray);
        }
        return $overdue;
    }

    /**
     * @param $instrument
     * @param $field
     * @return bool
     */
    private function isFieldInInstrument($instrument, $field)
    {
        $fields = $this->getProject()->forms[$instrument]['fields'];
        return array_key_exists($field, $fields);
    }

    private function processAddProjectRequest($inputs)
    {
        try {
            $this->emDebug($inputs);
            $this->setProject(new \Project($inputs['redcap_project_id']));
            $this->savePortalProjectInfoInREDCap($inputs);

        } catch (\Exception $e) {
            header("Content-type: application/json");
            http_response_code(404);
            echo json_encode(array($e->getMessage()));
        }
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
        return $token == $this->getClient()->getToken();
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
     * @param string $path
     */
    public function includeFile($path)
    {
        include_once $path;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Support
     */
    public function getSupport(): Support
    {
        return $this->support;
    }

    /**
     * @param Support $support
     */
    public function setSupport(Support $support): void
    {
        $this->support = $support;
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
     * @return Portal
     */
    public function getPortal(): Portal
    {
        return $this->portal;
    }

    /**
     * @param Portal $portal
     */
    public function setPortal(Portal $portal): void
    {
        $this->portal = $portal;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return array
     */
    public function getNotifications(): array
    {
        if (!$this->notifications) {
            $this->setNotifications();
        }
        return $this->notifications;
    }

    /**
     * @param array $notifications
     */
    public function setNotifications(): void
    {
        $path = dirname(__DIR__) . '/' . $this->PREFIX . '_' . $this->VERSION . "/language/Notifications.ini";
        $this->notifications = parse_ini_file($path);;
    }



    /**
     * @return \Stanford\ProjectPortal\ManagerEM
     */
    public function getManagerEm()
    {
        return $this->managerEm;
    }

    /**
     * @param \Stanford\ProjectPortal\ManagerEM $managerEm
     */
    public function setManagerEm($managerEm): void
    {
        $this->managerEm = $managerEm;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($status, $fees, $isLinked, $hasRMA = false, $approvedRMA = false): void
    {
        $this->state = Utilities::determineProjectState($status, $fees, $isLinked, $hasRMA, $approvedRMA);
    }


}