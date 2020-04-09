<?php
include_once('classes/Smsalert.php');

$apikey='';       // write your apikey in between ''
$senderid='';			 // write your senderid in between ''
$route='';               // write your route in between ''
$username = '';              // write your username in between ''
$pass='';					 // write your pass in between ''
$smsalert = (new Smsalert()) 
				->SetApiKey($apikey)
               	->SetUsername($username)
                ->SetRoute($route)
                ->SetPassword($pass)
                ->SetSender($senderid);

//======== for send sms ================
$numbers='8010551055'; //enter the number on which text to be messaged

$message=" other new Message From Smsalert Services"; // write your msg here between ""

$result = $smsalert->send($numbers,$message);


//========== for senderid list =============
 $result = $smsalert->getSenderid();

//=========== for get user profile===========
 $result = $smsalert->getUserProfile();


//=========== For Get Group List  ============
 $result = $smsalert->getGroupList();

//========== For Get Contact List ===========
 $result = $smsalert->getContactList();       


//============for Set Schedule Sms ===========
  $mobileno='9599162608'; //enter the number on which text to be messaged
  $text=" other new Message From Smsalert Services"; // write your msg here between "" 
  $schedule = "2020-04-09 5:49" ;              
  $result = $smsalert->ScheduleSms($mobileno,$text,$schedule);    

//============ For send sms using xml ==============
 $datas = array(
 			array(
 				'number'=>'9599162608',
 				'sms_body'=>'New Messages'
 			)
 );
   $result = $smsalert->send_sms_xml($datas);


//============ For Create Contact List  ==============
 	$number='8010551058'; //enter the number on which text to be messaged
    $grpname="WorKG"; // write your msg here between "" 
    $name = "Ramesh" ;      
    $result = $smsalert->CreateContact($grpname,$name,$number);

//============ For Create Contact List  ==============
	 $grpname='Test'; //enter the number on which text to be messaged
     $result = $smsalert->CreateGroup($grpname);	

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