
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
                ->authWithApikey("apikey")
                ->authWithUserIdPwd("username","pass")
                ->setRoute("route")
                ->setSender("senderid");

# Send Single Number
    $result = $smsalert->send("9999xxxxxx","Test Message");

# Send Multiple Number
    $result = $smsalert->send("9999xxxxxx,9998xxxxxx","Test Message");

# Schedule Sms For Single Number
    $result = $smsalert->send("9999xxxxxx","Test Message","2020-04-10 2:53");

# Schedule Sms For Multiple Number
    $result = $smsalert->send("9999xxxxxx,9998xxxxxx","Test Message","2020-04-10 2:53");

# Edit Schedule
    $result = $smsalert->editSchedule($batchid,$schedule);    

# Cancel Schedule
    $result = $smsalert->cancelSchedule($batchid); 

# Send Sms Xml
    $sms_datas = array(
            array(
                'number'=>'8010551055', 
                'sms_body'=>'New Messages'
            )
        );
    $result = $smsalert->sendSmsXml($sms_datas);   

# Get Senderid List
    $result = $smsalert->getSenderId();

# Get User Profile
    $result = $smsalert->getUserProfile();

# Update Profile
    $result = $smsalert->updateProfile("fname","lname","8010551055","test@gmail.com");
    
# Get Group List
    $result = $smsalert->getGroupList("10","1","desc");

# Create Group
    $result = $smsalert->createGroup("grpname");   

# Delete Group
    $result = $smsalert->deleteGroup("grpid");  

# Edit Group
    $result = $smsalert->editGroup("grpname","grpid");

# Send Group Sms
    $result = $smsalert->sendGroupSms("grpname","grpid","text msg","2020-04-10 2:53");           

# Get Group Contact List
    $result = $smsalert->getContactList("grpname");             

# Create Contact In Group
    $result = $smsalert->createContact("grpname","Demo","8010551055"); 

# Edit Contact
    $result = $smsalert->editContact("contactid","personname","8010551055");    

# Delete Contact
    $result = $smsalert->deleteContact("contactid"); 

# Import Xml Contact
    $grpname = 'groupname'; 
    $datas = array(array('person_name'=>'Demo Name','number'=>'8010551055'));
    $result = $smsalert->importXmlContact($datas,$grpname);          

# Get Template List
    $result = $smsalert->getTemplateList();

# Create Template
    $result = $smsalert->createTemplate("template name","template Msg");

# Edit Template
    $result = $smsalert->editTemplate("template name","template Msg","templateid");            

# Delete Template
    $result = $smsalert->deleteTemplate("templateid");

# Generate OTP
    $result = $smsalert->generateOtp("8010551055","Your Verificatoin no is [otp]");

# Validate OTP
    $result = $smsalert->validateOtp("8010551055","1234");

# Create Short Url
    $result = $smsalert->createShortUrl("url");

# Delete Short Url
    $result = $smsalert->deleteShortUrl("urlid");

# Send Sms Report
    $result = $smsalert->smsReport(10,1,1);

# Push Report
    $result = $smsalert->pushReport("8010551055","Msg","1234","dlrurl","2020-04-10 2:53");     

# Pull Report
    $result = $smsalert->pullReport("batchid");   

# Check Balance
    $result = $smsalert->balanceCheck(); 

## Support 
Email :  support@cozyvision.com
Phone :  080-1055-1055
