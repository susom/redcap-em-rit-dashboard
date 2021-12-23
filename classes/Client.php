<?php


namespace Stanford\ProjectPortal;

/**
 * Class Client
 * @package Stanford\ProjectPortal
 */
class Client
{
    private $token;

    private $jwtToken;

    private $portalUsername;

    private $portalPassword;

    private $portalBaseURL;

    private $guzzleClient;

    public function __construct($token, $portalUsername, $portalPassword, $portalBaseURL)
    {
        $this->setToken($token);

        $this->setPortalUsername($portalUsername);

        $this->setPortalPassword($portalPassword);

        $this->setPortalBaseURL($portalBaseURL);

        $this->setGuzzleClient(new \GuzzleHttp\Client());
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

    private function setJWTTokenIntoSession($jwtToken)
    {
        $_SESSION['project_portal_jwt_token'] = $jwtToken;
        $_SESSION['project_portal_jwt_token_created_at'] = time();
    }

    /**
     * @return string
     */
    public function getJwtToken()
    {
        if (!$this->jwtToken) {
            $this->setJwtToken();
        }
        return $this->jwtToken;
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setJwtToken()
    {
        try {
            if (isset($_SESSION['project_portal_jwt_token']) && $this->isJWTTokenStillValid()) {
                $this->jwtToken = $_SESSION['project_portal_jwt_token'];
            } else {
                $response = $this->getGuzzleClient()->post($this->getPortalBaseURL() . 'api/users/token/', [
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
                    if (property_exists($data, 'token')) {
                        $this->jwtToken = $data->token;
                        $this->setJWTTokenIntoSession($data->token);
                    } elseif (property_exists($data, 'access')) {
                        $this->jwtToken = $data->access;
                        $this->setJWTTokenIntoSession($data->access);
                    } else {
                        throw new \Exception("Could not find JWT token property.");
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
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
     * @return \GuzzleHttp\Client
     */
    public function getGuzzleClient(): \GuzzleHttp\Client
    {
        return $this->guzzleClient;
    }

    /**
     * @param \GuzzleHttp\Client $guzzleClient
     */
    public function setGuzzleClient(\GuzzleHttp\Client $guzzleClient): void
    {
        $this->guzzleClient = $guzzleClient;
    }
}