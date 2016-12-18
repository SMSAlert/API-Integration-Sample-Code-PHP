
## Overview

Sms Alert Php library for sending transactional/promotional SMS, through your custom code. Easy to integrate, you just need to write 2 lines of code to send SMS.

## Paramerer Details

* apikey : Api Key(This key is unique for every user, you can obtain it from your smsalert account http://www.smsalert.co.in/api)

* number : single or multiple 10 digit mobile numbers (seperated by comma)

* message : Message Content to be sent

* senderid : Receiver will see this as sender's ID (six chars)

* route : route name, allocated to your account(see API builder for exact value or contact support team).


## Usage 
# //include Class file
    include_once('classes/Smsalert.php');

# //create object and pass arguments
    $smsalert= new Smsalert("apikey","senderid","route");//change all 3 parameter values here

# //Send Singal Number
    $result = $smsalert->send("9999xxxxxx","Test Message");

# //Send Multiple Number
    $result = $smsalert->send("9999xxxxxx,9998xxxxxx","Test Message");

## Support 
Email :  support@cozyvision.com
Phone :  080-1055-1055
