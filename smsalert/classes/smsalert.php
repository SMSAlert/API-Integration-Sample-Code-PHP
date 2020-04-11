<?php
/**
 *  @author    Cozy Vision Technologies Pvt. Ltd.
 *  @copyright 2010-2016 cozyvision Technology Pvt Ltd.
 */
 include_once("smsalert/helper/Utility.php");
 class Smsalert{
	private $sender;       // declare senderid of user 
	private $route;         // declare route of user 
	private $url='http://www.smsalert.co.in';   // Define url 
    private $authParams=array();

    // function for sending smsalert
	public function send($mobileno,$text,$schedule=null)
	{	
        $url = $this->url.'/api/push.json';
        $params=array('sender'=>$this->sender,'mobileno'=>$mobileno,'text'=>$text);   
        if(!empty($schedule))
        {   
            $params['schedule'] = $schedule; // for Schedule Sms 
        }
        if(!empty($this->route))
        {
            $params['route']=$this->route; 
        }
        $params = array_merge($params,$this->authParams);
        return Utility::invoke_api($url,$params);
    }
   
    // function for Sender Id List
    public function getSenderId()
    {
    	$url = $this->url.'/api/senderlist.json';
		return Utility::invoke_api($url,$this->authParams);
    }

    // function for user profile
    public function getUserProfile()
    {
    	$url = $this->url.'/api/user.json';
		return Utility::invoke_api($url,$this->authParams);
    }

    //function for group list
    public function getGroupList($limit=10,$page=1,$order='desc')
    {
    	$url = $this->url.'/api/grouplist.json';
    	$params=array('limit'=>$limit,'page'=>$page,'order'=>$order);
        $params = array_merge($params,$this->authParams);
		return Utility::invoke_api($url,$params);
    }

    //function for contact list
    public function getContactList($groupid=null,$limit=10,$page=1,$order='desc')
    {
    	$url = $this->url.'/api/grouplist.json';
    	$params=array('group_id'=>$groupid,'limit'=>$limit,'page'=>$page,'order'=>$order);
        $params = array_merge($params,$this->authParams);
		return Utility::invoke_api($url,$params);
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
        if (array_key_exists("apikey",$this->authParams))
        {
            $user->addAttribute('apikey', $this->authParams['apikey']);  
        }else{
            $user->addAttribute('username', $this->authParams['user']);
            $user->addAttribute('password', $this->authParams['pwd']); 
        }        
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
        $url = 'http://www.smsalert.co.in/api/xmlpush.json';
        $params=array(
        'data'=>$xmldata); 
        return Utility::invoke_api($url,$params);
    }

    //function to Create Contact
    public function createContact($grpname,$name,$number)
    {
        $url = $this->url.'/api/createcontact.json';
        $params=array('grpname'=>$grpname,'name'=>$name,'number'=>$number);
        $params = array_merge($params,$this->authParams);
        return Utility::invoke_api($url,$params);
    }

    //function to create group
    public function createGroup($grpname)
    {
        $url = $this->url.'/api/creategroup.json';
        $params=array(
        'name'=>$grpname);
        $params = array_merge($params,$this->authParams);
        return Utility::invoke_api($url,$params);
    }

    //function to get template list
    public function getTemplateList()
    {
        $url = $this->url.'/api/templatelist.json';      
        return Utility::invoke_api($url,$this->authParams);
    }

    //function to set apikey
    function authWithApikey($apikey)
    {
        $this->authParams=array('apikey'=>$apikey);
        return $this;
    }

    //function to set apikey
    function authWithUserIdPwd($user,$pwd)
    {
        $this->authParams=array('user'=>$user,'pwd'=>$pwd);
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