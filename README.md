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
composer require lalamove/php:master
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

