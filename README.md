[![Build Status](https://travis-ci.org/yamdraco/lalamove-php.svg?branch=master)](https://travis-ci.org/yamdraco/lalamove-php) [![Coverage Status](https://coveralls.io/repos/github/yamdraco/lalamove-php/badge.svg?branch=master)](https://coveralls.io/github/yamdraco/lalamove-php?branch=master)

# Lalamove Unofficial Client Library for PHP
## Library Maintenance
This library is an unoffical library for lalamove api. Currently we are fixing all necessary bug and adding essential features to ensure the lirbary continues to meet your needs in accessing the Lalamove APIs. Non-critical issues will be closed. Any issue may be reopened if it is causing ongoing problem.

## Requirements
- PHP 5.5.0 or higher

## Installation
You can use **Composer**
### Composer
Follow the [install instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.
```
composer require lalamove/php:1.0.2
```

## Getting Started
### Quotation
The following the code we need to make quotation for SG
```
$body = array(
  "scheduleAt" => gmdate('Y-m-d\TH:i:s\Z', time() + 60 * 30)  // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time
  "serviceType" => "MOTORCYCLE",                              // string to pick the available service type
  "specialRequests" => array(),                               // array of strings available for the service type
  "requesterContact" => array(
    "name" => "Draco Yam",
    "phone" => "+6592344758"                                  // Phone number format must follow the format of your country
  ),  
  "stops" => array(
    array(
      "location" => array("lat" => "1.284318", "lng" => "103.851335"),
      "addresses" => array(
        "en_SG" => array(
          "displayString" => "1 Raffles Place #04-00, One Raffles Place Shopping Mall, Singapore",
          "country" => "SG"                                   // Country code must follow the country you are at
        )   
      )   
    ),  
    array(
      "location" => array("lat" => "1.278578", "lng" => "103.851860"),
      "addresses" => array(
        "en_SG" => array(
          "displayString" => "Asia Square Tower 1, 8 Marina View, Singapore",
          "country" => "SG"                                   // Country code must follow the country you are at
        )   
      )   
    )   
  ),  
  "deliveries" => array(
    array(
      "toStop" => 1,
      "toContact" => array(
        "name" => "Brian Garcia",
        "phone" => "+6592344837"                              // Phone number format must follow the format of your country
      ),  
      "remarks" => "ORDER #: 1234, ITEM 1 x 1, ITEM 2 x 2"
    )   
  )   
);

$request = new \Lalamove\Api\LalamoveApi('https://sandbox-rest.lalamove.com', <apiKey>, <apiSecret>, 'SG');
$result = $request->quotation($body);
```
Sample Response
```
{
  "totalFeeCurrency": "SGD",
  "totalFee": "10.0"
}
```

### Place Order
The Response from the quotation is needed for Place order API to lock the price
```
$body = array(
  "scheduleAt" => gmdate('Y-m-d\TH:i:s\Z', time() + 60 * 30)  // ISOString with the format YYYY-MM-ddTHH:mm:ss.000Z at UTC time
  "serviceType" => "MOTORCYCLE",                              // string to pick the available service type
  "specialRequests" => array(),                               // array of strings available for the service type
  "requesterContact" => array(
    "name" => "Draco Yam",
    "phone" => "+6592344758"                                  // Phone number format must follow the format of your country
  ),  
  "stops" => array(
    array(
      "location" => array("lat" => "1.284318", "lng" => "103.851335"),
      "addresses" => array(
        "en_SG" => array(
          "displayString" => "1 Raffles Place #04-00, One Raffles Place Shopping Mall, Singapore",
          "country" => "SG"                                   // Country code must follow the country you are at
        )   
      )   
    ),  
    array(
      "location" => array("lat" => "1.278578", "lng" => "103.851860"),
      "addresses" => array(
        "en_SG" => array(
          "displayString" => "Asia Square Tower 1, 8 Marina View, Singapore",
          "country" => "SG"                                   // Country code must follow the country you are at
        )   
      )   
    )   
  ),  
  "deliveries" => array(
    array(
      "toStop" => 1,
      "toContact" => array(
        "name" => "Brian Garcia",
        "phone" => "+6592344837"                              // Phone number format must follow the format of your country
      ),  
      "remarks" => "ORDER #: 1234, ITEM 1 x 1, ITEM 2 x 2"
    )   
  ),
  "quotedTotalFee" => array(
    "amount" => "10.0",
    "currency" => "SGD"
  )
);

$request = new \Lalamove\Api\LalamoveApi('https://sandbox-rest.lalamove.com', <apiKey>, <apiSecret>, 'SG');
$result = $request->postOrder($body);
```
Sample Response
```
{
  "customerOrderId": "a5232cd5-677d-49f8-8977-37380caeea72",    // use for all subsequence call such as getting order info / driver info
  "orderRef": "179802"                                          // the reference shown in driver app when driver arrived or used when calling our customer service
}
```

### Getting Order Information
Once an order is placed, you can query the result of the order every 45s, notice there is a rate limit on our system. *DO NOT call too frequently*.  
To get order information
```
$request = new \Lalamove\Api\LalamoveApi('https://sandbox-rest.lalamove.com', <apiKey>, <apiSecret>, 'SG');
$result = $request->getOrderStatus(<Order id such as a5232cd5-677d-49f8-8977-37380caeea72>);
```
Sample Response
```
{
  "driverId": "",
  "status": "ASSIGNING"     // During assigning, you are unable to get the driverId
}
```

### Getting Driver Information
Getting driver information will help your team to know who will come to pick up the order
```
$request = new \Lalamove\Api\LalamoveApi('https://sandbox-rest.lalamove.com', <apiKey>, <apiSecret>, 'SG');
$result = $request->getDriverInfo(<order id>, <driverId from the above>);
```
Sample Response
```
{
    "name": "David",
    "phone": "+6582121212"
}
```

### Cancel Order
Order can only be cancelled before the order is picked up and within 5 mins after the order is matched. Notice that each city is different for the cancellation buffer time, as long as the you are able to receive 200 as http status code, the cancellation is successful
```
$request = new \Lalamove\Api\LalamoveApi('https://sandbox-rest.lalamove.com', <apiKey>, <apiSecret>, 'SG');
$result = $request->cancelOrder(<order id>);
```
Sample Response but http code be 200 (success) or fail (non 200 response)
```
{}
```


## How to Submit a bug, issue or feature request
If you wish to submit a bug, issue, or feature request, then you can find the [issue here](https://github.com/yamdraco/lalamove-php/issues) and you can [create one here](https://github.com/yamdraco/lalamove-php/issues/new). For bug reporting, make sure you provide the following information
1. Your PHP version and framework (if any)
2. Your country and locale
3. Clear steps to reproduce the bug (mainly header and body and url)
4. A description of **what you expected to happen**
5. A description of **what actually happened**

## Releases
### 20180126 (v1.0.1)
* By Alpha
* Force body to be object at all condition during json_encode

### 20170825 (v1.0.0)
* By Draco
* Quotation, Place Order and Cancel API
* Get Order Info, Driver Info and Location API
* Continuous integration setup
* PHP Linter setup

