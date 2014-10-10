<?php

/**
 * Copyright 2014 Matt Acosta
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Handles the OAuth2 authentication flow for Battle.net clients.
 *
 * NOTE: Google_Client requires many more methods than Google_Auth_Abstract
 * suggests, so this class simply extends Google_Auth_OAuth2.
 */
class BattleNet_Auth_OAuth2 extends Google_Auth_OAuth2 {
  const BATTLENET_URL_BASE = 'https://{region}.battle.net';
  const BATTLENET_URL_BASE_CHINA = 'https://www.battlenet.com.cn';
  
  const BATTLENET_AUTH_PATH = '/oauth/authorize';
  const BATTLENET_TOKEN_PATH = '/oauth/token';
  
  /**
   * @var Google_Client The client used during authentication.
   */
  private $client;
  
  /**
   * @var string A copy of the state parameter used during authentication for
   *   CSRF protection.
   */
  private $state;
  
  /**
   * Creates a BattleNet_Auth_OAuth2 object.
   *
   * This does not start the authentication flow.
   */
  public function __construct(Google_Client $client) {
    parent::__construct($client);
    $this->client = $client;
  }
  
  /**
   * Overrides Google_Auth_OAuth2::createAuthUrl().
   */
  public function createAuthUrl($scope) {
    $params = array(
      'response_type' => 'code',
      'redirect_uri' => $this->client->getClassConfig($this, 'redirect_uri'),
      'client_id' => $this->client->getClassConfig($this, 'client_id'),
      'scope' => $scope
    );
    
    if (isset($this->state)) {
      $params['state'] = $this->state;
    }
    
    return $this->getAuthBase() . self::BATTLENET_AUTH_PATH . '?' . http_build_query($params, '', '&');
  }
  
  /**
   * Overrides Google_Auth_OAuth2::authenticate().
   */
  public function authenticate($code) {
    if (strlen($code) == 0) {
      throw new Google_Auth_Exception('Invalid code');
    }
    
    $request = new Google_Http_Request(
      $this->getAuthBase() . self::BATTLENET_TOKEN_PATH,
      'POST',
      array(),
      array(
        'code' => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $this->client->getClassConfig($this, 'redirect_uri'),
        'client_id' => $this->client->getClassConfig($this, 'client_id'),
        'client_secret' => $this->client->getClassConfig($this, 'client_secret')
      )
    );
    $request->disableGzip();
    $response = $this->client->getIo()->makeRequest($request);
    
    if ($response->getResponseHttpCode() == 200) {
      // The $this->token field is private, so this is a workaround to set the
      // created time before the variable is lost to us.
      $token = json_decode($response->getResponseBody(), TRUE);
      $token['created'] = time();
      $token = json_encode($token);
      
      $this->setAccessToken($token);
      return $token;
    }
    else {
      $decodedResponse = json_decode($response->getResponseBody(), TRUE);
      if ($decodedResponse != null && $decodedResponse['error']) {
        $decodedResponse = $decodedResponse['error'];
        if (isset($decodedResponse['error_description'])) {
          $decodedResponse .= ': ' . $decodedResponse['error_description'];
        }
      }
      throw new Google_Auth_Exception(
        sprintf('Error fetching OAuth2 access token, message: \'%s\'', $decodedResponse),
        $response->getResponseHttpCode()
      );
    }
  }
  
  /**
   * Determines the current base URL to use for the given configuration region.
   *
   * @return string The base URL to use for OAuth2 authorization requests.
   */
  protected function getAuthBase() {
    $region = $this->client->getClassConfig($this, 'region');
    if ($region == BattleNet_Config::REGION_CN) {
      return self::BATTLENET_URL_BASE_CHINA;
    }
    return str_replace('{region}', $region, self::BATTLENET_URL_BASE);
  }
  
  /**
   * Overrides Google_Auth_OAuth2::refreshToken().
   */
  public function refreshToken($refreshToken)
  {
    throw new Google_Exception('Not implemented');
  }
  
  /**
   * Overrides Google_Auth_OAuth2::refreshTokenWithAssertion().
   */
  public function refreshTokenWithAssertion($assertionCredentials = null) {
    throw new Google_Exception('Not implemented');
  }
  
  /**
   * Overrides Google_Auth_OAuth2::revokeToken().
   */
  public function revokeToken($token = NULL) {
    throw new Google_Exception('Not implemented');
  }
  
  /**
   * Overrides Google_Auth_OAuth2::setState().
   */
  public function setState($state) {
    parent::setState($state);
    $this->state = $state;  // We need a copy...
  }
}
