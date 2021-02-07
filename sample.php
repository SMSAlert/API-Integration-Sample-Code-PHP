<?php

include("vendor/autoload.php");
use SMSAlert\Lib\Smsalert\Smsalert;

$apikey		='';    // write your apikey in between ''
$senderid	='';	// write your senderid in between ''
$route		='';    // write your route in between ''
$username 	=''; 	// write your username in between ''
$pass		='';	// write your pass in between ''
$prefix		='';	// write your country code here eg. 91
$smsalert 	= (new Smsalert()) 		
	   ->authWithUserIdPwd($username,$pass)
	   ->setRoute($route)
	   ->setForcePrefix($prefix)
	   ->setSender($senderid);
//======== for send sms ================
$numbers='8010551055'; //enter the number on which text to be messaged
$message="Order again for test"; // write your msg here between ""
$schedule = "2020-05-20 2:50";  // write schedule date and time here between "" 
$reference = '122215';  //reference no. for delivered message
$dlrurl = 'https://webhook.site/de0b3ecf-f444-4bee-ae38-cad217dbe4b0';  //callback url for delivery notification   
$result = $smsalert->send($numbers,$message); // For Send Sms
$result = $smsalert->send($numbers,$message,$schedule); // For Schedule Sms
$result = $smsalert->send($numbers,$message,$schedule,$reference,$dlrurl); // For Push Report
//========== for edit Schedule =============
$batchid = '37909043'; //schedule batchid
$schedule = "2021-12-20 2:53"; //schedule date and time	   
$result = $smsalert->editSchedule($batchid,$schedule);

//========== for Cancel Schedule =============
$batchid = '37909043';  //schedule batchid
$result = $smsalert->cancelSchedule($batchid);

//============ For send sms using xml ==============
$datas = array(array('number'=>'8010551055','sms_body'=>'New Messages'));	
$result = $smsalert->sendSmsXml($datas);	   

//========== for senderid list =============
$result = $smsalert->getSenderId();

//=========== for get user profile===========
$result = $smsalert->getUserProfile();

//============ For Update Profile ======================
$fname = ''; // enter first name of user
$lname = ''; //enter last name of user
$number = ''; // enter contact no. of user
$emailid = ''; // enter  email of user             
$result = $smsalert->updateProfile($fname,$lname,$number,$emailid);	  

//=========== For Get Group List  ============
$limit=10; //writer limit no. to show group list
$page=1; // write page no. show no of pages
$order = "desc" ;  // write schedule date and time here between "" 
$result = $smsalert->getGroupList($limit,$page,$order);

//============ For Create group  ==============
$grpname='Groupsecond'; //enter the group name which you want to create
$result = $smsalert->createGroup($grpname);	

//============ For Delete group  ==============
$grid='2723'; //enter the group id to delete
$result = $smsalert->deleteGroup($grid);		

//============ For Edit group  ==============
$grid='2750'; //enter the group id to edit
$grpname= 'groupsend';
$result = $smsalert->editGroup($grpname,$grid);

// //============ For Send Group Sms ==============
$grid='2750'; //enter the group id
$text= 'Enter Messages Here'; //enter msg to send
$schedule= '2020-05-22 19:02'; //write date and time for schedule
$result = $smsalert->sendGroupSms($grid,$text,$schedule);    

//========== For Get Contact List ===========
$groupid='2750'; //enter group id 
$result = $smsalert->getContactList($groupid);      

//============ For Create Contact List  ==============
$name = "Ramesh";  //enter contact member name                
$number='8010585058'; //enter the number of member
$grpname="groupsend"; // enter group name in which you want to add     
$result = $smsalert->createContact($grpname,$name,$number);

//============ For Edit Contact ==============
$name = "Ramesh";  //enter contact member name                
$number='8010555058'; //enter the number of member
$contactid="2662106"; // enter contact id to edit     
$result = $smsalert->editContact($contactid,$name,$number);

//============ For Delete Contact ==============
$contactid="2662121"; // enter contact id to delete contact    
$result = $smsalert->deleteContact($contactid);	   

//============ For Import Contact ==============
$grpname = 'Groupsecond'; // enter Group name in which you want to add contact
$datas = array(array('person_name'=>'Ankit Sharma','number'=>'8999999999'));	    
$result = $smsalert->importXmlContact($datas,$grpname);	 	   

//============ For Template List  ==============
$result = $smsalert->getTemplateList();

//============ For Create Template ==============
$name='new template'; //Template Name
$text='This is new template'; //Enter Template Msg
$result = $smsalert->createTemplate($name,$text);

//============ For Edit Template  ==============
$name='new template'; //Template Name
$text='This is new templates ss'; //Enter Template Msg
$id='19270'; //enter template id to edit
$result = $smsalert->editTemplate($name,$text,$id);

//============ For Edit Template List  ==============
$id='19272'; //enter template id to delete
$result = $smsalert->deleteTemplate($id);

//============ For Generate Otp ======================
$mobileno = '8010551055'; // enter contact no. of user
$template = 'Your Verificatoin no is [otp]'; //mandatory to include [otp] tag in template
$result = $smsalert->generateOtp($mobileno,$template);	 

//============ For Validate Otp ======================
$mobileno = '8010551055'; // enter contact no. of 
$otp = '4720'; // enter otp sent on mobile             
$result = $smsalert->validateOtp($mobileno,$otp);	 

//============ For Create Short Url ======================
$url = ''; // enter url here between ""           
$result = $smsalert->createShortUrl($url);	  

//============ For Delete Short Url ======================
$urlid = '5e946585-fa6c-47b3-8564-38f08ba21ef9'; // enter url id to delete           
$result = $smsalert->deleteShortUrl($urlid);

//============ For Sent Sms Report ======================
$limit=10; //enter limit of data
$page=1; //enter pages
$schedule=1; // enter schedule          
$result = $smsalert->smsReport($limit,$page,$schedule);

//============ For Pull Report ======================  
$batchid = '37724276';
$result = $smsalert->pullReport($batchid);	   

//============ For Balance Check ======================
$result = $smsalert->balanceCheck(); 

if($result['status'] == 'success')
{
	echo 'success';
}
else
{
	die('Error ');
}

?>