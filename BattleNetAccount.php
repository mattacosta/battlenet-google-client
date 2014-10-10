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
 * Service definition for Battle.net accounts.
 */
class BattleNet_Service_Account extends Google_Service {
  /**
   * @var BattleNet_Service_Account_Resource
   */
  public $account;
  /**
   * @var BattleNet_Service_Profile_Resource
   */
  public $profile;
  
  /**
   * Constructs the internal representation of the Battle.net account service.
   */
  public function __construct(Google_Client $client) {
    parent::__construct($client);
    $this->serviceName = '';
    $this->servicePath = '';
    
    // constructor: service, serviceName, resourceName, resource
    $this->account = new BattleNet_Service_Account_Resource(
      $this, $this->serviceName, 'account',
      array(
        'methods' => array(
          'accountid' => array(
            'path' => 'account/user/id',
            'httpMethod' => 'GET',
            'parameters' => array()
          ),
          'battletag' => array(
            'path' => 'account/user/battletag',
            'httpMethod' => 'GET',
            'parameters' => array()
          )
        )
      )
    );
    $this->profile = new BattleNet_Service_Profile_Resource(
      $this, $this->serviceName, 'profile',
      array(
        'methods' => array(
          'sc2_profile' => array(
            'path' => 'sc2/profile/user',
            'httpMethod' => 'GET',
            'parameters' => array()
          ),
          'wow_profile' => array(
            'path' => 'wow/user/characters',
            'httpMethod' => 'GET',
            'parameters' => array()
          )
        )
      )
    );
  }
}

/**
 * A resource to obtain Battle.net account information.
 */
class BattleNet_Service_Account_Resource extends Google_Service_Resource {
  public function getAccountId($optParams = array()) {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('accountid', array($params));
  }
  
  public function getBattleTag($optParams = array()) {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('battletag', array($params));
  }
}

/**
 * A resource to obtain game profiles from a Battle.net account.
 */
class BattleNet_Service_Profile_Resource extends Google_Service_Resource {
  public function getStarcraftProfile($optParams = array()) {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('sc2_profile', array($params));
  }
  
  public function getWarcraftCharacters($optParams = array()) {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('wow_profile', array($params));
  }
}
