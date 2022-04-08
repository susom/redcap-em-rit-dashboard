<?php

namespace Stanford\ProjectPortal;

/**
 * Class Support
 * @package Stanford\ProjectPortal
 * @property \Stanford\ProjectPortal\Client $client
 * @property array $jiraIssueTypes;
 */
class Support
{
    private $client;

    private $jiraIssueTypes;

    /**
     * User constructor.
     * @param \Stanford\ProjectPortal\Client $client
     */
    public function __construct($client)
    {
        $this->setClient($client);
    }


    /**
     * @param int $redcapProjectId
     * @param string $summary
     * @param int $issueTypeId
     * @param string $description
     * @param null $portalProjectId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createJiraTicketViaPortal($redcapProjectId, $summary, $issueTypeId, $description, $portalProjectId = null, $redcapProjectName = null, $user_firstname = '', $user_lastname = '')
    {

        $jwt = $this->getClient()->getJwtToken();
        if (is_null($portalProjectId) || $portalProjectId == '') {
            $url = $this->getClient()->getPortalBaseURL() . 'api/issues/add-issue/';

        } else {
            $url = $this->getClient()->getPortalBaseURL() . 'api/projects/' . $portalProjectId . '/add-issue/';
        }
        $response = $this->getClient()->getGuzzleClient()->post($url, [
            'debug' => false,
            'headers' => [
                'Authorization' => "Bearer {$jwt}",
            ],
            'form_params' => [
                'redcap' => $redcapProjectId,
                'redcap_name' => $redcapProjectName,
                'summary' => $summary,
                'request_type' => $issueTypeId,
                'description' => $description,
                'raise_on_behalf_of' => USERID,
                'raise_on_behalf_of_firstname' => $user_firstname,
                'raise_on_behalf_of_lastname' => $user_lastname
            ],
        ]);

        if ($response->getStatusCode() < 300) {
            return json_decode($response->getBody(), true);
        }
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJiraIssueTypes(): array
    {
        if (!$this->jiraIssueTypes) {
            $this->setJiraIssueTypes();
        }
        return $this->jiraIssueTypes;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setJiraIssueTypes(): void
    {
        try {
            # get or update current jwt token to make requests to project portal api
            //$this->getProjectPortalJWTToken();

            $jwt = $this->getClient()->getJwtToken();
            $response = $this->getClient()->getGuzzleClient()->get($this->getClient()->getPortalBaseURL() . 'api/jira/request-types/', [
                'debug' => false,
                'headers' => [
                    'Authorization' => "Bearer {$jwt}",
                ]
            ]);
            if ($response->getStatusCode() < 300) {
                $data = json_decode($response->getBody());
                $values = $data->values;
                $jiraIssueTypes = array();
                foreach ($values as $value) {
                    $jiraIssueTypes[$value->id] = $value->name;
                }
                $this->jiraIssueTypes = $jiraIssueTypes;
            }
        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
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

}