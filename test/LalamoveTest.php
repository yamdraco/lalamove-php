<?php

use PHPUnit\Framework\TestCase;

if (!getenv('country')) {
  $Loader = new \josegonzalez\Dotenv\Loader('.env');
  // Parse the .env file
  $Loader->parse();
  // Send the parsed .env file to the $_ENV variable
  $Loader->putenv();
}

class LalamoveTest extends TestCase
{

  public function generateRandomString($length = 10)
  {
    $x = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    return substr(str_shuffle(str_repeat($x, ceil($length/strlen($x)))), 1, $length);
  }


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

  public function testAuthFail()
  {
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), 'abc123', 'abc123', getenv('country'));
    $result = $request->quotation($this->body);

    self::assertSame($result['code'], 'HTTP/1.1 401 Unauthorized');
  }

  public function testQuotation()
  {
    $results = [];
    $scheduleAt = gmdate('Y-m-d\TH:i:s\Z', time() + 60 * 30);
    $this->body['scheduleAt'] = $scheduleAt;
    $this->body['deliveries'][0]['remarks'] = $this->generateRandomString();
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), getenv('key'), getenv('secret'), getenv('country'));
    $result = $request->quotation($this->body);

    self::assertSame($result['code'], 'HTTP/1.1 200 OK');
    
    $content = json_decode($result['body']);

    $results['scheduleAt'] = $scheduleAt;
    $results['quotation'] = $content;
    return $results;
  }
  
  /**
   * @depends testQuotation
   */
  public function testPostOrder($results)
  {
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), getenv('key'), getenv('secret'), getenv('country'));
    $this->body['scheduleAt'] = $results['scheduleAt'];
    $this->body['quotedTotalFee'] = array(
      'amount' => $results['quotation']->totalFee,
      'currency' => $results['quotation']->totalFeeCurrency
    );
    // too frequent submission of the same body will cause 429 error
    // therefore adding random string inside the remark when testing
    $this->body['deliveries'][0]['remarks'] = $this->generateRandomString();
    $result = $request->postOrder($this->body);
    self::assertSame($result['code'], 'HTTP/1.1 200 OK');
    
    $results['orderId'] = json_decode($result['body']);
    return $results;
  }

  /**
   * @depends testPostOrder
   */
  public function testGetOrderStatus($results)
  {
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), getenv('key'), getenv('secret'), getenv('country'));
    $result = $request->getOrderStatus($results['orderId']->customerOrderId);
    self::assertSame($result['code'], 'HTTP/1.1 200 OK');
  }

  /**
   * @depends testPostOrder
   */
  public function testCancelOrder($results)
  {
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), getenv('key'), getenv('secret'), getenv('country'));
    $result = $request->cancelOrder($results['orderId']->customerOrderId);
    self::assertSame($result['code'], 'HTTP/1.1 200 OK');
  }

  public function testGetExistingOrderStatus()
  {
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), getenv('key'), getenv('secret'), getenv('country'));
    $result = $request->getOrderStatus("3dc4959b-8705-11e7-a723-06bff2d87e1b");
    self::assertSame($result['code'], 'HTTP/1.1 200 OK');
  }

  public function testGetDriverInfo()
  {
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), getenv('key'), getenv('secret'), getenv('country'));
    $result = $request->getDriverInfo('3dc4959b-8705-11e7-a723-06bff2d87e1b', '21712');
    self::assertSame($result['code'], 'HTTP/1.1 200 OK');
  }

  public function testGetDriverLocation()
  {
    $request = new \Lalamove\Api\LalamoveApi(getenv('host'), getenv('key'), getenv('secret'), getenv('country'));
    $result = $request->getDriverLocation('3dc4959b-8705-11e7-a723-06bff2d87e1b', '21712');
    self::assertSame($result['code'], 'HTTP/1.1 200 OK');
  }
}
