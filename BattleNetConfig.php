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
 * Provides configuration options for making authenticated API requests when
 * used with a Google_Client object.
 */
class BattleNet_Config extends Google_Config {
  const LIBVER = '1.0.0';
  
  const AUTH_CLASS = 'BattleNet_Auth_OAuth2';
  
  const BATTLENET_API_BASE = 'https://{region}.api.battle.net';
  const BATTLENET_API_BASE_CHINA = 'https://api.battlenet.com.cn';
  
  const REGION_US = 'us';
  const REGION_EU = 'eu';
  const REGION_KR = 'kr';
  const REGION_TW = 'tw';
  const REGION_CN = 'cn';
  
  private static $schema = array(
    'regions' => array(
      self::REGION_US,
      self::REGION_EU,
      self::REGION_KR,
      self::REGION_TW,
      self::REGION_CN
    )
  );
  
  /**
   * Creates a new BattleNet_Config object.
   *
   * @param string $region
   *   (optional) A supported Battle.net login region.
   * @param string $ini_file_location
   *   (optional) The location of an ini file to load.
   */
  public function __construct($region = self::REGION_US, $ini_file_location = null) {
    parent::__construct($ini_file_location);
    $this->setAuthClass(self::AUTH_CLASS);
    $this->setRegion($region);
  }
  
  /**
   * Overrides Google_Config::getBasePath().
   */
  public function getBasePath() {
    $region = $this->getRegion();
    if ($region == self::REGION_CN) {
      return self::BATTLENET_API_BASE_CHINA;
    }
    return str_replace('{region}', $region, self::BATTLENET_API_BASE);
  }
  
  /**
   * Returns the current Battle.net login region.
   */
  public function getRegion() {
    return $this->getClassConfig(self::AUTH_CLASS, 'region');
  }
  
  /**
   * Returns a list of supported Battle.net regions.
   */
  public static function getSupportedRegions() {
    return self::$schema['regions'];
  }
  
  /**
   * Sets the Battle.net login region.
   *
   * @param string $region
   *   A supported Battle.net region.
   *
   * @see getSupportedRegions()
   */
  public function setRegion($region) {
    if (!in_array($region, self::$schema['regions'])) {
      throw new Google_Exception('Unsupported Battle.net region');
    }
    $this->setClassConfig(self::AUTH_CLASS, 'region', $region);
  }
}
