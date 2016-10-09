<?php
include_once('classes/Smsalert.php');

$apikey='';       // write your apikey in between ''
$senderid='';			 // write your senderid in between ''
$route='transactional';      // write your route in between ''
$smsalert= new Smsalert($apikey,$senderid,$route);


$numbers='9015208266'; //enter the number on which text to be messaged

$message=" msg goes here"; // write your msg here between ""

$result = $smsalert->send($numbers,$message);
if($result['status'] == 'success')
{
	echo 'Message submitted with message Id'.$result['description']['batch_dtl'][0]['msgid'];
}
else
{
	die('Error sending SMS');
}

?>