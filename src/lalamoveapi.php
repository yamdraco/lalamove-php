<?php
/**
 * Copyright 2017 Lalamove
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * @description
 *
 * @author Draco
 */
namespace Lalamove\Api;

include ('request.php');

class LalamoveApi {
  public $host = '';
  public $key = '';
  public $secret = '';

  public $country = '';
  
  /**
   * Constructor for Lalamove API
   *
   * @param $host - domain with http / https
   * @param $apikey - apikey lalamove provide
   * @param $apisecret - apisecret lalamove provide
   * @param $country - two letter country code such as HK, TH, SG
   *
   */
  function __construct($host = '', $apiKey = '', $apiSecret = '', $country = '') {
    $this->host = $host;
    $this->key = $apiKey;
    $this->secret = $apiSecret;
    $this->country = $country;
  }

  /**
   * Make a http Request to get a quotation from lalamove API via guzzlehttp/guzzle
   * 
   * @param $body, the body of the json
   * @return the http response from guzzlehttp/guzzle, an exception will not be thrown
   *   2xx - http request is successful
   *   4xx - unsuccessful request, see body for error message and documentation for matching
   *   5xx - server error, please contact lalamove
   */
  public function quotation($body) {
    $request = new Request();
    $request->method = 'POST';
    $request->path = "/v2/quotations";
    $request->body = $body;
    $request->host = $this->host;
    $request->key = $this->key;
    $request->secret = $this->secret;
    $request->country = $this->country;
    return $request->query();
  }
}


