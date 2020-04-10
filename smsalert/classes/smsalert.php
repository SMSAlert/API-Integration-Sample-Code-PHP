<?php
/**
 *  @author    Cozy Vision Technologies Pvt. Ltd.
 *  @copyright 2010-2016 cozyvision Technology Pvt Ltd.
 */
 include_once("smsalert/helper/Utility.php");
 class Smsalert{
	private $apikey;         // declare api key of user 
	private $sender;       // declare senderid of user 
	private $route;         // declare route of user 
	private $url='http://www.smsalert.co.in';   // Define url 
	private $username;       // declare username of user
	private $pass;           // declare password of user

    //function authenticate parameters
    public function authenticateParams()
    {
        if(!empty($this->username) && !empty($this->pass))
        {  
            $params['user'] = $this->username;
            $params['pwd'] = $this->pass;
        }
        else
        {
            $params['apikey'] = $this->apikey;
        }
        return $params;
    }

	// function for sending smsalert
	public function send($mobileno,$text,$schedule=null)
	{		
		$url = $this->url.'/api/push.json';
        $params=array(
        'route'=>$this->route,
        'sender'=>$this->sender,
        'mobileno'=>$mobileno,
        'text'=>$text);   
        if(!empty($schedule))
        { 
            $params['schedule'] = $schedule;   // for Schedule Sms 
        }
        $paras = $this->authenticateParams();
        $params = array_merge($params,$paras);
		$Util = new Utility();
		return $Util->invoke_Api($url,$params);
    }
   
    // function for Sender Id List
    public function getSenderId()
    {
    	$url = $this->url.'/api/senderlist.json';
    	$params = $this->authenticateParams();
		$Util = new Utility();
		return $Util->invoke_Api($url,$params);
    }

    // function for user profile
    public function getUserProfile()
    {
    	$url = $this->url.'/api/user.json';
    	$params = $this->authenticateParams();
		$Util = new Utility();
		return $Util->invoke_Api($url,$params);
    }

    //function for group list
    public function getGroupList()
    {
    	$url = $this->url.'/api/grouplist.json';
    	$params=array(
        'limit'=>'10',
        'page'=>'1',
        'order'=>'desc');
        $auth_params = $this->authenticateParams();
        $params = array_merge($params,$auth_params);
		$Util = new Utility();
		return $Util->invoke_Api($url,$params);
    }

    //function for contact list
    public function getContactList($groupid=null,$limit=10,$page=1,$order='desc')
    {
    	$url = $this->url.'/api/grouplist.json';
    	$params=array(
            'group_id'=>$groupid,
            'limit'=>$limit,
            'page'=>$page,
            'order'=>$order
        );
        $paras = $this->authenticateParams();
        $params = array_merge($params,$paras);
		$Util = new Utility();
		return $Util->invoke_Api($url,$params);
    }

    //function for send sms using xml
    public  function sendSmsXml($sms_datas)
    {   if(is_array($sms_datas) && sizeof($sms_datas) == 0)
        {return false;}
$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<message>
</message>
XML;
        $msg = new SimpleXMLElement($xmlstr);
        $user = $msg->addChild('user');
        $user->addAttribute('username', $this->username);
        $user->addAttribute('password', $this->pass); 
        foreach($sms_datas as $sms_data){
            $sms = $msg->addChild('sms');
            $sms->addAttribute('text', $sms_data['sms_body']);
            $address = $sms->addChild('address');
            $address->addAttribute('from', $this->route);
            $address->addAttribute('to', $sms_data['number']);
        }
        if($msg->count() <= 1)
        { return false; }         
        $xmldata = $msg->asXML();
        echo $xmldata;
        $url = 'http://www.smsalert.co.in/api/xmlpush.json';
        $params=array(
        'data'=>$xmldata); 
        $Util = new Utility();
        return $Util->invoke_Api($url,$params);
    }

    //function to Create Contact
    public function createContact($grpname,$name,$number)
    {
        $url = $this->url.'/api/createcontact.json';
        $params=array('grpname'=>$grpname,'name'=>$name,'number'=>$number);
        $paras = $this->authenticateParams();
        $params = array_merge($params,$paras);
        $Util = new Utility();
        return $Util->invoke_Api($url,$params);
    }

    //function to create group
    public function createGroup($grpname)
    {
        $url = $this->url.'/api/creategroup.json';
        $params=array(
        'name'=>$grpname);
        $paras = $this->authenticateParams();
        $params = array_merge($params,$paras);
        $Util = new Utility();
        return $Util->invoke_Api($url,$params);
    }

    //function to get template list
    public function getTemplateList()
    {
        $url = $this->url.'/api/templatelist.json';      
        $params = $this->authenticateParams();
        $Util = new Utility();
        return $Util->invoke_Api($url,$params);
    }

    //function for set apikey
    public function setApikey($apikey)
    {
    	$this->apikey = $apikey;
    	return $this;
    }

    //function for set username
	public function setUsername($username)
    {
    	$this->username = $username;
    	return $this;
    }    

    //function for set password
    public function setPassword($password)
    {
    	$this->pass = $password;
    	return $this;
    } 

    //function for set route
    public function setRoute($route)
    {
    	$this->route = $route;
    	return $this;
    } 

    //function for set senderid
    public function setSender($sender)
    {
    	$this->sender = $sender;
    	return $this;
    } 
}


?>