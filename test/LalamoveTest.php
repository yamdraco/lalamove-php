<?php

namespace Lalamove\Api;

use Lalamove\Api\Lalamoveapi;
use Lalamove\Api\Request;
use PHPUnit\Framework\TestCase;

$Loader = new \josegonzalez\Dotenv\Loader('.env');
// Parse the .env file
$Loader->parse();
// Send the parsed .env file to the $_ENV variable
$Loader->toEnv();

class LalamoveTest extends TestCase {
  public $body = array(
    "serviceType" => "MOTORCYCLE",
    "specialRequests" => array(),
    "requesterContact" => array(
      "name" => "Draco Yam",
      "phone" => "+6592344758"
    ),
    "stops" => array(
      array(
        "location" => array("lat" => "1.284318", "lng" => "103.851335"),
        "addresses" => array(
          "en_SG" => array(
            "displayString" => "1 Raffles Place #04-00, One Raffles Place Shopping Mall, Singapore",
            "country" => "SG"
          )
        )
      ),
      array(
        "location" => array("lat" => "1.278578", "lng" => "103.851860"),
        "addresses" => array(
          "en_SG" => array(
            "displayString" => "Asia Square Tower 1, 8 Marina View, Singapore",
            "country" => "SG"
          )
        )
      )
    ),
    "deliveries" => array(
      array(
        "toStop" => 1,
        "toContact" => array(
          "name" => "Brian Garcia",
          "phone" => "+6592344837"
        ),
        "remarks" => "ORDER #: 1234, ITEM 1 x 1, ITEM 2 x 2"
      )
    )
  );

  public function testAuthFail() {
    $request = new Lalamoveapi($_ENV['host'], 'abc123', 'abc123', $_ENV['country']);
    $result = $request->quotation($this->body);

    self::assertSame($result->getStatusCode(), 401);
  }

  public function testQuotation() {
    $this->body['scheduleAt'] = $time = gmdate('Y-m-d\TH:i:s\Z', time() + 60 * 30);
    $request = new Lalamoveapi($_ENV['host'], $_ENV['key'], $_ENV['secret'], $_ENV['country']);
    $result = $request->quotation($this->body);

    self::assertSame($result->getStatusCode(), 200);
  }
}
