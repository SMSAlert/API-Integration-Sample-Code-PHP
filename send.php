<?php

include("smsalert/classes/Smsalert.php");

$apikey='';     // write your apikey in between '5e886b523c74c'
$senderid='';			 // write your senderid in between ''
$route='';               // write your route in between ''
$username = '';    // write your username in between ''
$pass='';		 // write your pass in between ''
$smsalert = (new Smsalert()) 
				->authWithApikey($apikey)
				->authWithUserIdPwd($username,$pass)
				->setRoute($route)
                ->setSender($senderid);

//======== for send sms ================
$numbers='8010551055'; //enter the number on which text to be messaged
$message="Messages For check multiple numbers"; // write your msg here between ""
$schedule = "2020-04-10 2:53" ;  // write schedule date and time here between ""    
$result = $smsalert->send($numbers,$message); // For Send Sms
$result = $smsalert->send($numbers,$message,$schedule); // For Schedule Sms

//========== for senderid list =============
 $result = $smsalert->getSenderId();

//=========== for get user profile===========
$result = $smsalert->getUserProfile();

//=========== For Get Group List  ============
$limit=10; //writer limit no. to show group list
$page=1; // write page no. show no of pages
$order = "desc" ;  // write schedule date and time here between "" 
$result = $smsalert->getGroupList($limit,$page,$order);

//========== For Get Contact List ===========
$groupid='2371';
$result = $smsalert->getContactList($groupid=null);      

//============ For send sms using xml ==============
$datas = array(
			array(
				'number'=>'8010551055',	
				'sms_body'=>'New Messages'
			) );	
$result = $smsalert->sendSmsXml($datas);

//============ For Create Contact List  ==============
$name = "Ramesh";  //enter contact member name                
$number='8010555058'; //enter the number of member
$grpname="Workg"; // enter group name in which you want to add     
$result = $smsalert->createContact($grpname,$name,$number);

//============ For Create Contact List  ==============
$grpname='Tests'; //enter the group name which you want to create
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