## Overview

![SMS Alert Logo](banner.jpg)

SMS Alert PHP library for sending transactional/promotional SMS, through your custom code. Easy to integrate, just write 2 lines of code to send SMS.

To send SMS you need an account on [SMS Alert](https://www.smsalert.co.in), if you do not have an account, signup for a free demo account, to test the service, then after you can purchase credits to use it in your application.

## Installation

You can install **smsalert/php-sdk** via composer or by downloading the source.

### Via Composer:

**smsalert/php-sdk** is available on Packagist as the
[`smsalert/php-sdk`](https://packagist.org/packages/smsalert/php-sdk) package:

```
composer require smsalert/php-sdk
```

### Paramerer Details

```
SMSALERT_USER 	: username of your smsalert account
SMSALERT_PWD 	: password of your smsalert account
MOBILENO	: single or multiple mobile numbers (seperated by comma), with or without country code
TEXT	  	: message content to be sent
SCHEDULE	: future schedule date and time when you wish to send sms
```

### Usage

#### 1. include Class file
```
include("vendor/autoload.php");
use SMSAlert\Lib\Smsalert\Smsalert;
```

#### 2. create object and set authentication parameter
```
$SMSALERT_USER = ''; //change here
$SMSALERT_PWD  = ''; //change here

$smsalert      = (new Smsalert())
		->authWithUserIdPwd($SMSALERT_USER,$SMSALERT_PWD);
```
    
#### 3. send sms
```
$MOBILENO      = ''; //change here
$TEXT          = ''; //change here

$smsalert->setSender('CVDEMO')
         ->send($MOBILENO, $TEXT); 
```

### For Advanced Users

#### send schedule sms
```
$MOBILENO      = ''; //change here
$TEXT          = ''; //change here
$SCHEDULE      = ''; //change here

$smsalert->setSender('CVDEMO')
         ->send($MOBILENO, $TEXT, $SCHEDULE); 
```

#### set route 
```
$smsalert->setRoute('transactional');
```

#### set senderid 
```
$smsalert->setSender('CVDEMO'); 
```
	
#### set force prefix for countrycode 
```
$smsalert->setForcePrefix('91'); 
```

#### Support
```
Email :  support@cozyvision.com
Phone :  (+91)-80-1055-1055
```
