<?php


namespace Stanford\ProjectPortal;

use ExternalModules\ExternalModules;

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

    private $accessToken;

    private $refreshToken;

    private $expiryTimestamp;

    private $refreshExpiryTimestamp;

    private $prefix;

    public function __construct($prefix)
    {

        $this->setPrefix($prefix);

        $this->setGuzzleClient(new \GuzzleHttp\Client(['headers' => ['ORIGIN' => $_SERVER['SERVER_NAME']]]));

        $this->setToken(ExternalModules::getSystemSetting($this->getPrefix(), 'project-portal-api-token'));

        $this->setPortalUsername(ExternalModules::getSystemSetting($this->getPrefix(), 'portal-username'));

        $this->setPortalPassword(ExternalModules::getSystemSetting($this->getPrefix(), 'portal-password'));

        $this->setPortalBaseURL(ExternalModules::getSystemSetting($this->getPrefix(), 'portal-base-url'));

        $this->setAccessToken(ExternalModules::getSystemSetting($this->getPrefix(), 'access-token'));

        $this->setRefreshToken(ExternalModules::getSystemSetting($this->getPrefix(), 'refresh-token'));

        $this->setExpiryTimestamp(ExternalModules::getSystemSetting($this->getPrefix(), 'expiry-timestamp'));

        $this->setRefreshExpiryTimestamp(ExternalModules::getSystemSetting($this->getPrefix(), 'refresh-expiry-timestamp'));
    }


    /**
     * check is jwt token is still valid current expiration time is 2 days
     * @return bool
     */
    private function isJWTTokenStillValid()
    {
        return false;
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


    private function getNewAccessToken()
    {
        try {
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
                    $this->setAccessToken($data->access);
                    ExternalModules::setSystemSetting($this->getPrefix(), 'access-token', (string)$data->access);
                    $this->setRefreshToken($data->refresh);
                    ExternalModules::setSystemSetting($this->getPrefix(), 'refresh-token', (string)$data->refresh);
                    $this->setExpiryTimestamp((string)(time() + 60 * 60 * 24 * 2));
                    ExternalModules::setSystemSetting($this->getPrefix(), 'expiry-timestamp', (string)(time() + 60 * 60 * 24 * 2));
                    $this->setRefreshExpiryTimestamp((string)(time() + 60 * 60 * 24 * 7));
                    ExternalModules::setSystemSetting($this->getPrefix(), 'refresh-expiry-timestamp', (string)(time() + 60 * 60 * 24 * 7));
                    $this->setJWTTokenIntoSession($data->access);
                } else {
                    throw new \Exception("Could not find JWT token property.");
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function refreshExisingAccessToken()
    {
        try {
            // refresh token will expire after one week. if expired generate a new access/refresh tokens
            if ((int)$this->getRefreshExpiryTimestamp() > time()) {
                $response = $this->getGuzzleClient()->post($this->getPortalBaseURL() . 'api/users/refresh/', [
                    'debug' => false,
                    'form_params' => [
                        'refresh' => $this->getRefreshToken(),
                    ],
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ]
                ]);
                if ($response->getStatusCode() < 300) {
                    $data = json_decode($response->getBody());
                    if (property_exists($data, 'access')) {
                        $this->jwtToken = $data->access;
                        $this->setAccessToken((string)$data->access);
                        ExternalModules::setSystemSetting($this->getPrefix(), 'access-token', (string)$data->access);
                        $this->setJWTTokenIntoSession($data->access);
                        $this->setExpiryTimestamp(time() + 60 * 60 * 24 * 2);
                        ExternalModules::setSystemSetting($this->getPrefix(), 'expiry-timestamp', (string)(time() + 60 * 60 * 24 * 2));
                    } else {
                        throw new \Exception("Could not find JWT token property.");
                    }
                }
            } else {
                $this->getNewAccessToken();
            }
        } catch (\Exception $e) {
            return $this->getNewAccessToken();
        }
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function setJwtToken()
    {
        try {
            if ($this->isJWTTokenStillValid()) {
                $this->jwtToken = $_SESSION['project_portal_jwt_token'];
            } else {
                // if any of the tokens are expty or timestamp then generate a new one.
                // also this is useful for old jwt package installed in r2p2
                if (empty($this->getAccessToken()) || empty($this->getRefreshToken()) || empty($this->getExpiryTimestamp())) {
                    $this->getNewAccessToken();
                    // if token is not expired then use access token by setting EM setting to jwtToken param
                } elseif ((int)$this->getExpiryTimestamp() > time()) {
                    $this->jwtToken = $this->getAccessToken();
                    $this->setJWTTokenIntoSession($this->getAccessToken());
                    // this we need to refresh our access token
                } else {
                    $this->refreshExisingAccessToken();
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return string
     */
    private function getPortalUsername()
    {
        return $this->portalUsername;
    }

    /**
     * @param string $redcapUsername
     */
    private function setPortalUsername($redcapUsername)
    {
        $this->portalUsername = $redcapUsername;
    }

    /**
     * @return string
     */
    private function getPortalPassword()
    {
        return $this->portalPassword;
    }

    /**
     * @param string $redcapPassword
     */
    private function setPortalPassword($redcapPassword)
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

    /**
     * @return mixed
     */
    private function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    private function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    private function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     */
    private function setRefreshToken($refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return mixed
     */
    private function getExpiryTimestamp()
    {
        return $this->expiryTimestamp;
    }

    /**
     * @param mixed $expiryTimestamp
     */
    private function setExpiryTimestamp($expiryTimestamp): void
    {
        $this->expiryTimestamp = $expiryTimestamp;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return mixed
     */
    private function getRefreshExpiryTimestamp()
    {
        return $this->refreshExpiryTimestamp;
    }

    /**
     * @param mixed $refreshExpiryTimestamp
     */
    private function setRefreshExpiryTimestamp($refreshExpiryTimestamp): void
    {
        $this->refreshExpiryTimestamp = $refreshExpiryTimestamp;
    }


}