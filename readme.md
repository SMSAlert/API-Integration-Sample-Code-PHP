
## Overview

Sms Alert Php library for sending transactional/promotional SMS, through your custom code. Easy to integrate, you just need to write 2 lines of code to send SMS.

## Paramerer Details

* apikey : Api Key(This key is unique for every user, you can obtain it from your smsalert account http://www.smsalert.co.in/api)

* number : single or multiple 10 digit mobile numbers (seperated by comma)

* message : Message Content to be sent

* senderid : Receiver will see this as sender's ID (six chars)

* route : route name, allocated to your account(see API builder for exact value or contact support team).


## Usage 
# include Class file
    include_once('smsalert/classes/Smsalert.php');

# create object and pass arguments
    $smsalert = (new Smsalert()) 
				->setApiKey("apikey")
               	->setUsername("username")
                ->setRoute("route")
                ->setPassword("pass")
                ->setSender("senderid");

# Send Single Number
    $result = $smsalert->send("9999xxxxxx","Test Message");

# Send Multiple Number
    $result = $smsalert->send("9999xxxxxx,9998xxxxxx","Test Message");

# Schedule Sms For Single Number
    $result = $smsalert->send("9999xxxxxx","Test Message","2020-04-10 2:53");

# Schedule Sms For Multiple Number
    $result = $smsalert->send("9999xxxxxx,9998xxxxxx","Test Message","2020-04-10 2:53");

# Get Senderid List
    $result = $smsalert->getSenderId();

# Get User Profile
    $result = $smsalert->getUserProfile();

# Get Group List
    $result = $smsalert->getGroupList();

# Get Group Contact List
    $result = $smsalert->getContactList("grpname");

# Send Sms Xml
	$sms_datas = array(
			array(
				'number'=>'8010551055',	
				'sms_body'=>'New Messages'
			)
		);
    $result = $smsalert->sendSmsXml($sms_datas);                

# Create Contact In Group
    $result = $smsalert->createContact("grpname","Demo","8010551055"); 

# Create A Group
    $result = $smsalert->createGroup("grpname");     

# Get Template List
    $result = $smsalert->getTemplateList();     


## Support 
Email :  support@cozyvision.com
Phone :  080-1055-1055
