<?php
/**
 * @descroption
 * An http Request object for sending request to lalamove server via GuzzleHttp
 *
 * @author Draco
 */
namespace Lalamove\Api;

class Request {
  public $method = "GET";
  public $body = array();
  public $host = '';
  public $path = '';
  public $header = array();

  public $key = '';
  public $secret = '';
  public $country = '';

  public $ch = null;

  /**
   * Create the signature for the 
   * @param $time, time to create the signature (should use current time, same as the Authorization timestamp)
   *
   * @return a signed signature using the secret
   */
  public function getSignature($time) {
    $_encryptBody = '';
    if ($this->method == "GET") {
      $_encryptBody = $time."\r\n.$this->method.\r\n".$this->path."\r\n\r\n";
    } else {
      $_encryptBody = $time."\r\n".$this->method."\r\n".$this->path."\r\n\r\n".json_encode($this->body);
    }
    return hash_hmac("sha256", $_encryptBody, $this->secret);
  }

  public function buildHeader() {
    $time = time() * 1000;
    return [
      "X-Request-ID" => uniqid(),
      "Content-type" => "application/json; charset=utf-8",
      "Authorization" => "hmac ".$this->key.":".$time.":".$this->getSignature($time),
      "Accept"=> "application/json",
      "X-LLM-Country"=> $this->country
    ];
  }

  public function query() {
    $client = new \GuzzleHttp\Client();

    return $client->request($this->method, $this->host.$this->path, [
      'headers' => $this->buildHeader(),
      'json' => $this->body,
      'http_errors' => false
    ]);
  }
}
