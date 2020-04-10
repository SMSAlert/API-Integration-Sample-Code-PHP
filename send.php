<?php

include("smsalert/classes/Smsalert.php");

$apikey='5e886b523c74c';     // write your apikey in between ''
$senderid='VIEWIT';			 // write your senderid in between ''
$route='demo';               // write your route in between ''
$username = 'Subashbist';    // write your username in between ''
$pass='SmsAlert@123';		 // write your pass in between ''
$smsalert = (new Smsalert()) 
				->setApiKey($apikey)
               	->setUsername($username)
                ->setRoute($route)
                ->setPassword($pass)
                ->setSender($senderid);

//======== for send sms ================
$numbers='8010551055'; //enter the number on which text to be messaged
$message=" New Other Messages"; // write your msg here between ""
$schedule = "2020-04-10 2:53" ;  // write schedule date and time here between ""    
$result = $smsalert->send($numbers,$message); // For Send Sms
$result = $smsalert->send($numbers,$message,$schedule); // For Schedule Sms

//========== for senderid list =============
 $result = $smsalert->getSenderId();

//=========== for get user profile===========
$result = $smsalert->getUserProfile();

//=========== For Get Group List  ============
$result = $smsalert->getGroupList();

//========== For Get Contact List ===========
$groupid='2371';
$result = $smsalert->getContactList($groupid=null);      

//============for Set Schedule Sms ===========
$mobileno='8010551055'; //enter the number between ""
$text=" other new Message From Smsalert Services"; // write your msg here between "" 
$schedule = "2020-04-09 5:49" ;  // write schedule date and time here between ""             
$result = $smsalert->scheduleSms($mobileno,$text,$schedule);    

//============ For send sms using xml ==============
$datas = array(
			array(
				'number'=>'8010551055',	
				'sms_body'=>'New Messages'
			) );	
$result = $smsalert->sendSmsXml($datas);

//============ For Create Contact List  ==============
$name = "Ramesh";  //enter contact member name                
$number='8010551058'; //enter the number of member
$grpname="mygroup"; // enter group name in which you want to add     
$result = $smsalert->createContact($grpname,$name,$number);

//============ For Create Contact List  ==============
$grpname='Test'; //enter the group name which you want to create
$result = $smsalert->createGroup($grpname);	

//============ For Create Contact List  ==============
 $result = $smsalert->getTemplateList();

if($result['status'] == 'success')
{
	echo 'success';
}
else
{
	die('Error ');
}

?>