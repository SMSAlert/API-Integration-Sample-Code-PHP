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
    private $authParams=array();  // Define Authparams  
    private $prefix;    

    //For Auth Params
    public function getAuthParams()
    {
        if(empty($this->authParams))
        {
            die('Please Enter Apikey OR Username & password');
        }else{
            return $this->authParams;
        }
    }
    
    //For Format Number
    private function formatNumber($mobileno)
    {    
         $prefix = $this->setForcePrefix('91');
         $mobileno = explode(',',$mobileno);
         $nos = preg_replace('/[^0-9]/', '', $mobileno);
         $valid_no=array();
         if(is_array($nos))
            {           
                foreach($nos as $no){
                    $no = ltrim(ltrim($no, '+'),'0'); //remove leading + and 0
                    $no = (substr($no,0,strlen($prefix))!=$prefix) ? $prefix.$no : $no;
                    $match = preg_match("/^(\+)?(".$prefix.")?0?\d{10}$/",$no);
                    if($match)
                    { $valid_no[] = $no; }  
                }
            }
         return $num =implode(',', $valid_no);           
    }

    //For Set Force Prefix
    private function setForcePrefix($prefix)
    {    
       return $prefix;
    }

    // For Sending Smsalert
    public function send($mobileno,$text,$schedule=null)
    {   
        $url = $this->url.'/api/push.json';
        $params=array('sender'=>$this->sender,'mobileno'=>$this->formatNumber($mobileno),'text'=>$text);   
        if(!empty($schedule))
        {   
            $params['schedule'] = $schedule; // for Schedule Sms 
        }
        if(!empty($this->route))
        {
            $params['route']=$this->route; 
        }
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }
    
    // For Edit Scheudle
    public function editSchedule($batchid,$schedule)
    {
        $url = $this->url.'/api/modifyschedule.json';
        $params=array('batchid'=>$batchid,'schedule'=>$schedule);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    // For Cancel Scheudle
    public function cancelSchedule($batchid)
    {
        $url = $this->url.'/api/cancelschedule.json';
        $params=array('batchid'=>$batchid);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    // For Sender Id List
    public function getSenderId()
    {
        $url = $this->url.'/api/senderlist.json';
        return Utility::invoke_api($url,$this->getAuthParams());
    }

    // For User Profile
    public function getUserProfile()
    {
        $url = $this->url.'/api/user.json';
        return Utility::invoke_api($url,$this->getAuthParams());
    }

    // For Send Sms Using xml
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
        $url = $this->url.'/api/xmlpush.json';
        $params=array('data'=>$xmldata); 
        return Utility::invoke_api($url,$params);
    }

    //For Group List
    public function getGroupList($limit=10,$page=1,$order='desc')
    {
        $url = $this->url.'/api/grouplist.json';
        $params=array('limit'=>$limit,'page'=>$page,'order'=>$order);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Create Group
    public function createGroup($grpname)
    {
        $url = $this->url.'/api/creategroup.json';
        $params=array('name'=>$grpname);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Delete Group
    public function deleteGroup($id)
    {
        $url = $this->url.'/api/deletegroup.json';
        $params=array('id'=>$id);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Edit Group
    public function editGroup($grpname,$id)
    {
        $url = $this->url.'/api/updategroup.json';
        $params=array('id'=>$id,'name'=>$grpname);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Send Group Sms
    public function sendGroupSms($grpname,$grid,$text,$schedule=null)
    {
        $url = $this->url.'/api/grouppush.json';
        $params=array('id'=>$grid,'name'=>$grpname,'text'=>$text,'schedule'=>$schedule,'sender'=>$this->sender);
        $params['route']= !empty($this->route) ? $this->route : '';
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Contact List
    public function getContactList($groupid,$limit=10,$page=1,$order='desc')
    {
        $url = $this->url.'/api/contactlist.json';
        $params=array('group_id'=>$groupid,'limit'=>$limit,'page'=>$page,'order'=>$order);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Create Contact
    public function createContact($grpname,$name,$number)
    {
        $url = $this->url.'/api/createcontact.json';
        $params=array('grpname'=>$grpname,'name'=>$name,'number'=>$this->formatNumber($number));
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Edit Contact
    public function editContact($id,$name,$number)
    {
        $url = $this->url.'/api/updatecontact.json';
        $params=array('id'=>$id,'name'=>$name,'number'=>$this->formatNumber($number));
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }

    //For Delete Contact
    public function deleteContact($id)
    {
        $url = $this->url.'/api/deletecontact.json';
        $params=array('id'=>$id);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);
    }
    //For Contat create with xml
    public function importXmlContact($sms_datas=null,$grpname)
    {
       if(is_array($sms_datas) && sizeof($sms_datas) == 0)
        {return false;}
        $xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<group>
</group>
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
        $user->addAttribute('grp_name',$grpname);
        $members = $msg->addChild('members');
        foreach($sms_datas as $sms_data)
        {
            $member = $members->addChild('member');
            $member->addAttribute('name', $sms_data['person_name']);
            $member->addAttribute('number', $sms_data['number']);
        }   
        if($msg->count() <= 1)
        { return false; }         
        $xmldata = $msg->asXML();
        $url = $this->url.'/api/createcontactxml.json';
        $params=array('data'=>$xmldata); 
        return Utility::invoke_api($url,$params);
    }

    //Template List
    public function getTemplateList()
    {
        $url = $this->url.'/api/templatelist.json';      
        return Utility::invoke_api($url,$this->getAuthParams());
    }

    //Create Template
    public function createTemplate($name,$text)
    {
        $url = $this->url.'/api/createtemplate.json'; 
        $params=array('name'=>$name,'text'=>$text);
        $params = array_merge($params,$this->getAuthParams());     
        return Utility::invoke_api($url,$params);
    }
    
    //Edit Template
    public function editTemplate($name,$text,$id)
    {
        $url = $this->url.'/api/updatetemplate.json'; 
        $params=array('name'=>$name,'text'=>$text,'id'=>$id);
        $params = array_merge($params,$this->getAuthParams());     
        return Utility::invoke_api($url,$params);
    }

    //Edit Template
    public function deleteTemplate($id)
    {
        $url = $this->url.'/api/deletetemplate.json'; 
        $params=array('id'=>$id);
        $params = array_merge($params,$this->getAuthParams());     
        return Utility::invoke_api($url,$params);
    }

    //Check Balance
    public function balanceCheck()
    {
        $url = $this->url.'/api/creditstatus.json';
        return Utility::invoke_api($url,$this->getAuthParams());
    }

    //Update Profile
    public function updateProfile($fname,$lname,$number,$emailid)
    {
        $url = $this->url.'/api/updateprofile.json';
        $params=array('firstname'=>$fname,'lastname'=>$lname,'mobilenumber'=>$this->formatNumber($number),'emailid'=>$emailid);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params);   
    }

    //Generate Otp
    //mandatory to include [otp] tag in template content
    public function generateOtp($mobileno,$template)
    {
        $url = $this->url.'/api/mverify.json';
        $params=array('sender'=>$this->sender,'mobileno'=>$this->formatNumber($mobileno),'template'=>$template);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params); 
    }

    //Validate Otp
    public function validateOtp($mobileno,$code)
    {
        $url = $this->url.'/api/mverify.json';
        $params=array('code'=>$code,'mobileno'=>$this->formatNumber($mobileno));
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params); 
    }

    // Create Short Url
    public function createShortUrl($longurl)
    {
        $url = $this->url.'/api/createshorturl.json';
        $params=array('url'=>$longurl);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params); 
    }

    // Create Short Url
    public function deleteShortUrl($urlid)
    {
        $url = $this->url.'/api/deleteshorturl.json';
        $params=array('id'=>$urlid);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params); 
    }

    //Sent Sms Report
    public function smsReport($limit=10,$page=1,$schedule=1)
    {
        $url = $this->url.'/api/smscampaignlog.json';
        $params=array('limit'=>$limit,'page'=>$page,'schedule'=>$schedule);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params); 
    }

    //Push Report
    public function pushReport($mobileno,$text,$reference,$dlrurl,$schedule=null)
    {
        $url = $this->url.'/api/push.json';
        $dlrurl = (parse_url($dlrurl, PHP_URL_HOST) == 'localhost') ? urlencode($dlrurl) : $dlrurl;   
        $params=array('sender'=>$this->sender,'mobileno'=>$this->formatNumber($mobileno),'text'=>$text,
                      'reference'=>$reference, 'dlrurl'=>$dlrurl,'schedule'=>$schedule);
        $params['route']= !empty($this->route) ? $this->route : '';
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params); 
    }

    //Pull Report
    public function pullReport($batchid)
    {   
        $url = $this->url.'/api/pull.json';
        $params=array('batchid'=>$batchid);
        $params = array_merge($params,$this->getAuthParams());
        return Utility::invoke_api($url,$params); 
    }

    //Set Apikey
    function authWithApikey($apikey)
    {
        $this->authParams=array('apikey'=>$apikey);
        return $this;
    }

    //Set Username and Password
    function authWithUserIdPwd($user,$pwd)
    {
        $this->authParams=array('user'=>$user,'pwd'=>$pwd);
        return $this;
    }

    //Set Route
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    } 

    //Set Senderid
    public function setSender($sender)
    {
        $this->sender = $sender;
        return $this;
    } 
}
?>