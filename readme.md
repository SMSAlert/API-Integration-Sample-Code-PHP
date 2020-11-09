## Overview

*Sms Alert PHP library for sending transactional/promotional SMS, through your custom code. Easy to integrate, just write 2 lines of code to send SMS.*

## Paramerer Details

```
SMSALERT_USER 	: username of your smsalert account
SMSALERT_PWD 	: password of your smsalert account
MOBILENO	: single or multiple mobile numbers (seperated by comma), with or without country code
TEXT	  	: message content to be sent
$SCHEDULE	: future schedule date and time when you wish to send sms
```

## Usage

#### include Class file
include_once('smsalert/classes/Smsalert.php');

#### create object and set authentication parameter
```
$SMSALERT_USER = ''; //change here
$SMSALERT_PWD  = ''; //change here

$smsalert      = (new Smsalert())
		->authWithUserIdPwd($SMSALERT_USER,$SMSALERT_PWD);
```
    
#### send quick sms
```
$MOBILENO      = ''; //change here
$TEXT          = ''; //change here

$smsalert->setSender('VIEWIT')
         ->send($MOBILENO, $TEXT); 
```

#### send schedule sms
```
$MOBILENO      = ''; //change here
$TEXT          = ''; //change here
$SCHEDULE      = ''; //change here

$smsalert->setSender('VIEWIT')
         ->send($MOBILENO, $TEXT, $SCHEDULE); 
```

#### set route 
```
$smsalert->setRoute('transactional');
```

#### set senderid 
```
$smsalert->setSender('VIEWIT'); 
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
