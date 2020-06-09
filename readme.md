## Overview

**Sms Alert Php library for sending transactional/promotional SMS, through your custom code. Easy to integrate, you just need to write 2 lines of code to send SMS.**

## Paramerer Details

* username : username of smsalert
* password : password of smsalert
* number : single or multiple mobile numbers (seperated by comma)
* message : Message Content to be sent

## Usage 
# include Class file
    include_once('smsalert/classes/Smsalert.php');

# create object and set authentication parameter
    $SMSALERT_USER = '';  //enter your smsalert username
    $SMSALERT_PWD  = '';  //enter your smsalert password
    $smsalert      = (new Smsalert())
                     ->authWithUserIdPwd($SMSALERT_USER,$SMSALERT_PWD);
    
# send quick sms
    $MOBILENO      = ''; // valid mobile number including country code without leading 0 or + symbol
                            multiple numbers can be sent seperated by comma(,)
    $TEXT          = ''; //sms content to be sent without encoded                           
    $smsalert->setSender('VIEWIT')
             ->send($MOBILENO,$TEXT); 

# send schedule sms
     $MOBILENO      = ''; // valid mobile number including country code without leading 0 or + symbol
                             multiple numbers can be sent seperated by comma(,)
     $TEXT          = ''; // sms content to be sent without encoded    
     $SCHEDULE      = ''; // to schedule your messages
     $smsalert->setSender('VIEWIT')
             ->send($MOBILENO,$TEXT,$SCHEDULE); 

# set route 
    $smsalert->setRoute('transactional');

# set senderid 
    $smsalert->setSender('VIEWIT'); 
	
# set force prefix for countrycode 
    $smsalert->setForcePrefix('91'); 	

## Support 
Email :  support@cozyvision.com
Phone :  080-1055-1055
