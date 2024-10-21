<?php

namespace Stanford\ProjectPortal;

# test commit to trigger Github actions build for all branches.
#require __DIR__ . '/vendor/autoload.php';
require_once("emLoggerTrait.php");
require_once("classes/User.php");
require_once("classes/Support.php");
require_once("classes/Client.php");
require_once("classes/Portal.php");
require_once("classes/Entity.php");
require_once("classes/ManagerEM.php");
require_once("classes/Utilities.php");

const MAJOR_VERSION = 7;
use ExternalModules\ExternalModules;
use REDCap;
use Records;
use ExternalModules\AbstractExternalModule;
use Sabre\DAV\Exception;
use Stanford\ProjectPortal\Client;
use Stanford\ProjectPortal\Support;
use Stanford\ProjectPortal\User;
use Stanford\ProjectPortal\ManagerEM;

define('DEVELOPMENT_STATUS', 0);
define('PRODUCTION_STATUS', 1);
define('ARCHIVED_STATUS', 2);
define('COMPLETED_STATUS', 3);

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

    const HAS_FEES = 'has_fees';

    const HAS_RMA = 'has_rma';

    const RMA_APPROVED = 'rma_approved';
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

    private $templates;

    private $entity;
    /**
     * ProjectPortal constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // exclude surveys when large number of surveys are loaded a database lock error gets triggered.
        if (!$this->isSurveyPage()) {
            $this->setClient(new Client($this->PREFIX));

            $this->setManagerEm(new \Stanford\ProjectPortal\ManagerEM($this->getClient(), $this->PREFIX));


            $this->setEntity(new Entity());

            if ($_GET && isset($_GET['pid']) && $_GET['pid'] != null) {

                $this->setProject(new \Project($this->getProjectId()));

                //$this->setProjects($this->getEnabledProjects());

                $this->setPortal(new Portal($this->getClient(), $this->getProjectId(), $this->getProject()->project['app_title'], $this->getREDCapProjectStatus()));

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
    }

    public function checkProjectsRMACron()
    {
        $projects = self::query("select project_id, app_title from redcap_projects where status = 1", []);

        while ($project = $projects->fetch_assoc()) {
            $id = $project['project_id'];
            $url = $this->getUrl("cron/entity/check_overdue_payments.php", true) . '&pid=' . $id;
            $this->getClient()->getGuzzleClient()->request('GET', $url, array(\GuzzleHttp\RequestOptions::SYNCHRONOUS => true));
            $this->emDebug("running cron for $url on project " . $project['app_title']);
        }
    }


    /**
     * this is a cron that processes RMA and custom charges.
     * @return void
     */
    public function processCustomCharges()
    {
        $id = ExternalModules::getSystemSetting($this->PREFIX, 'external-modules-manager-redcap-project');
        $url = $this->getUrl("cron/portal/process_ems.php", true) . '&pid=' . $id;
        $this->getClient()->getGuzzleClient()->request('GET', $url, array(\GuzzleHttp\RequestOptions::SYNCHRONOUS => true));
    }

    // override code function to allow any logged-in user to access the dashboard.
    public function redcap_module_link_check_display($project_id, $link)
    {
        if (ExternalModules::isNoAuth()) {
            // Anyone can view NOAUTH pages.
            // Remember, redcap_module_link_check_display() is currently only called for pages defined in config.json.
            return $link;
        }

        if ($this->isSuperUser()) {
            // superusers can see all pages
            return $link;
        }

        $username = ExternalModules::getUsername();
        if (!empty($project_id) && $username) {
            return $link;
        }

        return null;
    }

    public function getREDCapProjectStatus()
    {
        if (!is_null($this->getProject()->project['completed_time'])) {
            return COMPLETED_STATUS;
        } else {
            return $this->getProject()->project['status'];
        }
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
        // exclude surveys from loading contact admin button
        if (!$this->isSurveyPage()) {
            $this->includeFile("views/contact_admin_button.php");
        }
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
            $this->getManagerEm()->updateProjectEMUtil($_POST['redcap_project_id']);

            $result = $this->getEntity()->generateProjectEMUsageArray(filter_var($_POST['redcap_project_id'], FILTER_SANITIZE_NUMBER_INT));
            header('Content-Type: application/json');
            echo json_encode($result);
        } elseif ($this->getRequest() == "get_em_monthly_charges") {
            if (!isset($_POST['month'])) {
                throw new \LogicException("Charges month is missing!");
            }
            if (!isset($_POST['year'])) {
                throw new \LogicException("Charges year is missing!");
            }
            if (!is_numeric($_POST['month']) or !in_array($_POST['month'], [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])) {
                throw new \LogicException("Month has wrong format!");
            }
            if (!is_numeric($_POST['year']) or ($_POST['year'] < 2020 or $_POST['year'] > 2025)) {
                throw new \LogicException("Year has wrong format!");
            }
            if (isset($_POST['project_id']) and !is_numeric($_POST['project_id'])) {
                throw new \LogicException("Project id has wrong format!");
            }
            $result = $this->getEntity()->getEMMonthlyCharges(filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT), filter_var($_POST['month'], FILTER_SANITIZE_NUMBER_INT), filter_var($_POST['project_id'], FILTER_SANITIZE_NUMBER_INT));
            header('Content-Type: application/json');
            echo json_encode($result);
        } elseif ($this->getRequest() == "get_custom_charges") {
            $result = $this->getManagerEm()->getManagerEMObject()->processCustomCharges();
            header('Content-Type: application/json');
            echo json_encode($result);
        } elseif ($this->getRequest() == "update_charge_ids") {
            if (!isset($_POST['updated_ids'])) {
                throw new \LogicException("Ids are missing!");
            }
            $updatedIds = json_decode($_POST['updated_ids'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \LogicException("Ids json is not valid! " . json_last_error());
            }
            $result = $this->getEntity()->updateEMChargesWithR2P2Id($updatedIds);
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
                    'content' => '(' . $this->getProjectId() . ') Overdue payment for month of  ' . date("F", (int)strtotime('00-' . $item['month'] . '-01')) . '-' . $item['year'] . '<br>' . $this->getEntity()->makeExternalModuleHtmlTable($item['external_modules']),
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
        $this->exitAfterHook();
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
    public function setState($status, $fees, $isLinked, $hasRMA = false, $rmaStatus = false): void
    {
        $approvedRMA = in_array($rmaStatus, [2, 6, 7]);
        $this->state = Utilities::determineProjectState($status, $fees, $isLinked, $hasRMA, $approvedRMA);
    }

    public function getNotificationProjects()
    {
        $result = [];
        $sql = "select redcap_projects.project_id as project_id, SUM(if(has_maintenance_fees = 1,maintenance_fees,0)) as total_fees, SUM(amount) as total_custom
from redcap_projects
        LEFT JOIN redcap_entity_project_external_modules_usage reemc
              on redcap_projects.project_id = reemc.project_id
        LEFT JOIN redcap_entity_custom_charges recc on redcap_projects.project_id = recc.project_id
WHERE is_em_enabled = 1 AND reemc.project_id is NOT NULL  GROUP BY redcap_projects.project_id";

        if (isset($_GET['project_status'])) {
            $status = htmlspecialchars($_GET['project_status']);
            $sql = "select redcap_projects.project_id as project_id, SUM(if(has_maintenance_fees = 1,maintenance_fees,0)) as total_fees, SUM(amount) as total_custom
from redcap_projects
        LEFT JOIN redcap_entity_project_external_modules_usage reemc
              on redcap_projects.project_id = reemc.project_id
        LEFT JOIN redcap_entity_custom_charges recc on redcap_projects.project_id = recc.project_id
WHERE is_em_enabled = 1 AND redcap_projects.status = '$status' AND reemc.project_id is NOT NULL  GROUP BY redcap_projects.project_id";

        }

        $q = db_query($sql);


        $has_rma = isset($_GET[self::HAS_RMA])?htmlspecialchars($_GET[self::HAS_RMA]):null;
        $rma_approved = isset($_GET[self::RMA_APPROVED])?htmlspecialchars($_GET[self::RMA_APPROVED]):null;
        $has_fees = isset($_GET[self::HAS_FEES])?htmlspecialchars($_GET[self::HAS_FEES]):null;
        $pta_status = isset($_GET['pta_status'])?htmlspecialchars($_GET['pta_status']):null;
        // manually set the portal so we can make calls to r2p2 api.
        $this->setPortal(new Portal($this->getClient()));

        if($has_rma == 0 AND ($rma_approved == 1 or $pta_status)) {
            throw new \Exception("Wrong combination or parameters!");
        }

        while ($row = db_fetch_assoc($q)) {
            // if we need projects without RMA. then check only if it has fees or not.
            if(is_null($has_rma)) {
                if ($has_fees == 1) {
                    if (($row['total_fees'] + $row['total_custom']) > 0) {
                        $result[] = $row['project_id'];
                    }
                }else{
                    if(($row['total_fees'] + $row['total_custom']) == 0){
                        $result[] = $row['project_id'];
                    }
                }
                continue;
            }else{
                // we need RMA.
                $r2p2 = $this->getPortal()->getR2P2ForREDCapProject($row['project_id']);

                if($has_rma == 1) {
                    // if no RMA then skip
                    if (empty($r2p2['rma'])) {
                        continue;
                    }
                }else{
                    // if RMA exists then skip
                    if (!empty($r2p2['rma'])) {
                        continue;
                    }
                }

                if (isset($_GET[self::HAS_FEES]) AND $has_fees == 1) {
                    // if we want projects with fees. skip projects without fees.
                    if (($row['total_fees'] + $row['total_custom']) == 0) {
                        continue;
                    }
                }elseif (isset($_GET[self::HAS_FEES]) AND $has_fees == 0){
                    // if we want projects without fees. skip projects with fees.
                    if(($row['total_fees'] + $row['total_custom']) > 0){
                        continue;
                    }
                }

                // if pta is requested but r2p2 pta does not match the requested status. then skip
                if(isset($_GET['pta_status'])){
                    if(empty($r2p2['rma']) or $pta_status != $r2p2['pta']['status']){
                        continue;
                    }
                }


                // if we want projects with RMA approved
                if(isset($_GET[self::RMA_APPROVED]) AND $rma_approved == 1){
                    // if we want projects with RMA approved. skip not approved ones.
                    if(!in_array($r2p2['rma']['status'], array(2,6,7))){
                            continue;
                        }
                }elseif(isset($_GET[self::RMA_APPROVED]) AND $rma_approved == 0){
                    // if we want projects with RMA NOT approved. skip approved ones.
                    if(in_array($r2p2['rma']['status'], array(2,6,7))){
                            continue;
                        }
                }
                $result[] = $row['project_id'];
            }

        }
        // remove any duplicate project ids.
        return array_unique($result);
    }

    public function prepareOnBehalfUser($array)
    {
        $user = $this->framework->getUser();
        $username = $user->getUsername();
        $user = ExternalModules::getUserInfo($username);
        $array['on_behalf_of_email'] = $user['user_email'];
        $array['on_behalf_of_username'] = $user['username'];
        $array['on_behalf_of_first_name'] = $user['user_firstname'];
        $array['on_behalf_of_last_name'] = $user['user_lastname'];
        return $array;
    }

    public function getAjaxFiles($dir, $folder)
    {
        $result = [];
        $files = scandir($dir);
        unset($files[array_search('.', $files, true)]);
        unset($files[array_search('..', $files, true)]);
        // test commitq
        // prevent empty ordered elements
        if (count($files) < 1)
            return [];


        foreach ($files as $file) {

            if (is_dir($dir . '/' . $file)) {
                $result = array_merge(self::getAjaxFiles($dir . '/' . $file, $folder . '/' . $file), $result);
            } else {
                $parts = explode('.', $file);
                $result[$parts[0]] = $this->getUrl($folder . '/' . $file, false, true);
            }

        }
        return $result;
    }

    public static function isUserProjectAdmin()
    {
        if (defined('PROJECT_ID') and (!defined('NOAUTH') || NOAUTH == false)) {

            //this function return right for main user when hit it with survey respondent!!!!!
            $right = REDCap::getUserRights();
            $user = $right[USERID];
            if ($user['design'] === "1") {
                return true;
            }
        }
        if (defined('SUPER_USER') && SUPER_USER == "1") {
            return true;
        }

        return false;
    }

    public function getEmailTemplates($key)
    {
        if (!$this->templates) {
            $this->setEmailTemplates();
        }
        foreach ($this->templates as $template) {
            if ($template['template_name'] == $key) {
                return $template;
            }
        }
        return [];
    }

    /**
     * @return void
     */
    public function setEmailTemplates()
    {
        $this->templates = $this->getSubSettings('emails_templates', $this->getProjectId());;
    }

    public function sendEmail($emails, $subject, $body)
    {
        foreach ($emails as $email) {
            $object = new \Message();
            $object->setTo($email);
            $object->setFrom('redcap@stanford.edu');
            $object->setSubject($subject);
            $object->setBody($body); //format message??

            $result = $object->send();
            //$module->emDebug($to, $from, $subject, $msg, $result);

            // Send Email
            if ($result == false) {
                throw new \Exception('Error sending mail: ' . $email->getSendError() . ' with ' . json_encode($email));
            }
        }

    }
}