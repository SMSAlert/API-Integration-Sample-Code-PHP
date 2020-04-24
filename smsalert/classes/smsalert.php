<?php
/**
 *  @author    Cozy Vision Technologies Pvt. Ltd.
 *  @copyright 2010-2020 Cozy Vision Technologies Pvt. Ltd.
 */
 include(dirname(__DIR__,1)."/vendor/guzzle/vendor/autoload.php");
 use GuzzleHttp\Client;
 
 class Smsalert{
    private $sender;       // declare senderid of user 
    private $route;         // declare route of user 
    private $url='http://www.smsalert.co.in';   // Define url
    private $authParams=array();  // Define Authparams  
    private $prefix;    
	
	/*****************************************************************************************
    * used to set apikey * * * * * * * * *  * * * * * * * * * * * * * * * * * * * * * * * * *
    *****************************************************************************************/
    function authWithApikey($apikey)
    {
        $this->authParams = array('apikey'=>$apikey);
        return $this;
    }

    /*****************************************************************************************
    * used to set username and password * * * * * * * * *  * * * * * * * * * * * * * * * * * * 
    *****************************************************************************************/
    function authWithUserIdPwd($user,$pwd)
    {
        $this->authParams = array('user'=>$user,'pwd'=>$pwd);
        return $this;
    }

    /*****************************************************************************************
    * used to set route * * * * * * * * *  * * * * * * * * * * * * * * * * * * * * * * * * *
    *****************************************************************************************/
    public function setRoute($route)
    {
        $this->route     = $route;
        return $this;
    } 

    /*****************************************************************************************
    * used to set sender id * * * * * * * * *  * * * * * * * * * * * * * * * * * * * * * * * *
    *****************************************************************************************/
    public function setSender($sender)
    {
        $this->sender    = $sender;
        return $this;
    }
	
    /*****************************************************************************************
    * Internal function for authenticate parameters to be used only for this class.  
    *****************************************************************************************/
    private function getAuthParams()
    {
        if(empty($this->authParams))
        {
            die('Please Enter Apikey OR Username & password');
        }else{
            return $this->authParams;
        }
    }
    
    /*****************************************************************************************
    * Internal function for format number to be used only for this class.
    *****************************************************************************************/
    private function formatNumber($mobileno)
    {    
         $prefix   = $this->setForcePrefix('91');
         $mobileno = explode(',',$mobileno);
         $nos      = preg_replace('/[^0-9]/', '', $mobileno);
         $valid_no = array();
         if(is_array($nos))
            {           
                foreach($nos as $no){
                    $no         = ltrim($no,'0');
                    $no         = (substr($no,0,strlen($prefix))!=$prefix) ? $prefix.$no : $no;
                    $valid_no[] = $no;  
                }
            }
         return $num =implode(',', $valid_no);           
    }

    /*****************************************************************************************
    * Internal function for set force prefix to be used only for this class.
    *****************************************************************************************/
    private function setForcePrefix($prefix)
    {    
       return $prefix;
    }
    
    /*****************************************************************************************
    * used to send or schedule sms
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * mobileno(mandatory)  - valid mobile number including country code without leading 0 or + symbol
    *                        multiple numbers can be sent seperated by comma(,)
    * text(mandatory)      - sms content to be sent without encoded 
    * schedule(optional)   - schedule your messages eg : '2020-06-10 12:00:02';
    * $reference(optional) - reference no. for delivered msg report eg : '125546';
    * $dlrurl(optional)    - callback url for delivery notification(url encoded format) 
                             eg : http://www.test.com/dlr.php
    *****************************************************************************************/
    public function send($mobileno,$text,$schedule=null,$reference=null,$dlrurl=null)
    {   
        $url    = $this->url.'/api/push.json';
        $params = array('sender'=>$this->sender,'mobileno'=>$this->formatNumber($mobileno),'text'=>$text);  
        if(!empty($schedule))
        {   
            $params['schedule'] = $schedule; // for Schedule Sms 
        }
        if(!empty($this->route))
        {
            $params['route']    = $this->route; 
        }
        if(!empty($dlrurl)) // for push report
        {
            $dlrurl             = (parse_url($dlrurl, PHP_URL_HOST) == 'localhost') ? urlencode($dlrurl) : $dlrurl;
            $params['dlrurl']   = $dlrurl;
            if(!empty($reference))
            {
                $params['reference'] = $reference; 
            }
            else{
                die('you must use reference parameter to use DLR callback url');
            }   
        }
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody()->getContents(),TRUE); 
        return $body;
    }
    
    /*****************************************************************************************
    * used to edit schedule sms
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * batchid(mandatory)    - batch id of the scheduled sms, received while sending the sms through API
    * schedule(mandatory)   - Date and time for updated schedule (Format: YYYY-MM-DD HH:MM:SS)
                              eg : '2020-06-10 12:00:02';
    *****************************************************************************************/
    public function editSchedule($batchid,$schedule)
    {
        $url      = $this->url.'/api/modifyschedule.json';
        $params   = array('batchid'=>$batchid,'schedule'=>$schedule);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * used to cancel schedule sms
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * batchid(mandatory)  - batch id of the scheduled sms, received while sending the sms through API
    *****************************************************************************************/
    public function cancelSchedule($batchid)
    {
        $url 	  = $this->url.'/api/cancelschedule.json';
        $params   = array('batchid'=>$batchid);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * used to send sms report
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * limit(optional)     - to get the list of records available per page. Default value for page is 10.
    * page(optional)      - to get the list of records from the respective pages. Default value for page is 1.
    * schedule(optional)  - schedule of sms report
    *****************************************************************************************/
    public function smsReport($limit=10,$page=1,$schedule=1)
    {
        $url      = $this->url.'/api/smscampaignlog.json';
        $params   = array('limit'=>$limit,'page'=>$page,'schedule'=>$schedule);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * used to pull sms report
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * batchid(mandatory)  - batch id received in response to every push request
    *****************************************************************************************/
    public function pullReport($batchid)
    {   
        $url      = $this->url.'/api/pull.json';
        $params   = array('batchid'=>$batchid);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;   
    }

    /*****************************************************************************************
    * used to retrieve senderid list * * * * * * * * *  * * * * * * * * * * * * * * * * * * * 
    *****************************************************************************************/
    public function getSenderId()
    {
        $url      = $this->url.'/api/senderlist.json';
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $this->getAuthParams(),'http_errors'=>false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * used to retrieve user profile * * * * * * * * *  * * * * * * * * * * * * * * * * * * * *
    *****************************************************************************************/
    public function getUserProfile()
    {
        $url      = $this->url.'/api/user.json';
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $this->getAuthParams(), 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to send sms xml push api
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * sms_datas(mandatory)  - array of number and msg to send sms
                              eg:array(array('number'=>'8010551055','sms_body'=>'New Messages'));
    *****************************************************************************************/
    public  function sendSmsXml($sms_datas)
    {   if(is_array($sms_datas) && sizeof($sms_datas) == 0)
        {return false;}
$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<message>
</message>
XML;
        $msg  = new SimpleXMLElement($xmlstr);
        $user = $msg->addChild('user');
        if (array_key_exists("apikey",$this->authParams))
        {
            $user->addAttribute('apikey', $this->authParams['apikey']);  
        }else{
            $user->addAttribute('username', $this->authParams['user']);
            $user->addAttribute('password', $this->authParams['pwd']); 
        }        
        foreach($sms_datas as $sms_data){
            $sms     = $msg->addChild('sms');
            $sms->addAttribute('text', $sms_data['sms_body']);
            $address = $sms->addChild('address');
            $address->addAttribute('from', $this->route);
            $address->addAttribute('to', $sms_data['number']);
        }
        if($msg->count() <= 1)
        { return false; }         
        $xmldata  = $msg->asXML();
        $url      = $this->url.'/api/xmlpush.json';
        $params   = array('data'=>$xmldata); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to retrieve group list
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * limit(optional)   - to get the list of records available per page. Default value for page is 10
    * page(optional)    - to get the list of records from the respective pages. Default value for page is 1
    * order(optional)   - to get the list of records in 'desc' order by default.
     *****************************************************************************************/
    public function getGroupList($limit=10,$page=1,$order='desc')
    {
        $url      = $this->url.'/api/grouplist.json';
        $params   = array('limit'=>$limit,'page'=>$page,'order'=>$order);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to create group
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * grpname(mandatory)   - group name, that you wish to create
     *****************************************************************************************/
    public function createGroup($grpname)
    {
        $url      = $this->url.'/api/creategroup.json';
        $params   = array('name'=>$grpname);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to delete group
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * grpid(mandatory)   - group id, that you wish to delete
     *****************************************************************************************/
    public function deleteGroup($grpid)
    {
        $url      = $this->url.'/api/deletegroup.json';
        $params   = array('id'=>$grpid);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to edit group
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * grpname(mandatory) - the group name that you want to modified
    * grpid(mandatory)   - group id, that you wish to modified
     *****************************************************************************************/
    public function editGroup($grpname,$grpid)
    {
        $url      = $this->url.'/api/updategroup.json';
        $params   = array('id'=>$grpid,'name'=>$grpname);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to send group sms
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * grpid(mandatory)    - group id, that you wish to send sms
    * text(mandatory)     - sms content to be sent without encoded
    * schedule(optional)  - date and time for schedule sms for group (Format: YYYY-MM-DD HH:MM:SS)
                            eg : '2020-06-10 12:00:02';
    *****************************************************************************************/
    public function sendGroupSms($grpid,$text,$schedule=null)
    {
        $url             = $this->url.'/api/grouppush.json';
        $params          = array('id'=>$grpid,'text'=>$text,'schedule'=>$schedule,'sender'=>$this->sender);
        $params['route'] = !empty($this->route) ? $this->route : '';
        $params          = array_merge($params,$this->getAuthParams());
        $client          = new Client();
        $response        = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body            = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to retrieve contact list
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * grpid(mandatory)  - group id, to get contact list of group
    * limit(optional)   - to get the list of records available per page. Default value for page is 10
    * page(optional)    - to get the list of records from the respective pages. Default value for page is 1
    * order(optional)   - to get the list of records in 'desc' order by default.
    *****************************************************************************************/
    public function getContactList($groupid,$limit=10,$page=1,$order='desc')
    {
        $url      = $this->url.'/api/contactlist.json';
        $params   = array('group_id'=>$groupid,'limit'=>$limit,'page'=>$page,'order'=>$order);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to create contact
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * grpname(mandatory)  - group name in which you want to create contact
    * name(mandatory)     - contact name of the person
    * number(mandatory)   - contact number of the person
    *****************************************************************************************/
    public function createContact($grpname,$name,$number)
    {
        $url      = $this->url.'/api/createcontact.json';
        $params   = array('grpname'=>$grpname,'name'=>$name,'number'=>$this->formatNumber($number));
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to edit contact
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * contactid(mandatory) - contact Number Id
    * name(mandatory)      - contact Name of the person
    * number(mandatory)    - contact Number of the person
    *****************************************************************************************/
    public function editContact($contactid,$name,$number)
    {
        $url      = $this->url.'/api/updatecontact.json';
        $params   = array('id'=>$contactid,'name'=>$name,'number'=>$this->formatNumber($number));
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to delete contact
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * contactid(mandatory) - contact number id that wish you to delete
    *****************************************************************************************/
    public function deleteContact($id)
    {
        $url      = $this->url.'/api/deletecontact.json';
        $params   = array('id'=>$id);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to import contact
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * sms_datas(mandatory)  - array of number and msg to create contact
                              eg:array(array('person_name'=>'Ankit Sharma','number'=>'8999999999'));
    * grpname(mandatory)    - group name to add contact
    *****************************************************************************************/
    public function importXmlContact($sms_datas,$grpname)
    {
       if(is_array($sms_datas) && sizeof($sms_datas) == 0)
        {return false;}
        $xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<group>
</group>
XML;
        $msg   = new SimpleXMLElement($xmlstr);
        $user  = $msg->addChild('user');
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
        $url     = $this->url.'/api/createcontactxml.json';
        $params  = array('data'=>$xmldata); 
        return Utility::invoke_api($url,$params);
    }

    /*****************************************************************************************
    * used to retrieve template list * * * * * * * * *  * * * * * * * * * * * * * * * * * * * 
    *****************************************************************************************/
    public function getTemplateList()
    {
        $url      = $this->url.'/api/templatelist.json';      
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $this->getAuthParams(), 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to create template
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * name(mandatory)  - template name
    * text(mandatory)  - sms content of template without encoded
    *****************************************************************************************/
    public function createTemplate($name,$text)
    {
        $url      = $this->url.'/api/createtemplate.json'; 
        $params   = array('name'=>$name,'text'=>$text);
        $params   = array_merge($params,$this->getAuthParams());     
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }
    
    /*****************************************************************************************
    * used to edit template
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * name(mandatory)  - name of template
    * text(mandatory)  - sms content of template without encoded
    * id(mandatory)    - template id that you wish to edit
    *****************************************************************************************/
    public function editTemplate($name,$text,$id)
    {
        $url      = $this->url.'/api/updatetemplate.json'; 
        $params   = array('name'=>$name,'text'=>$text,'id'=>$id);
        $params   = array_merge($params,$this->getAuthParams());     
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to delete template
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * id(mandatory)    - template id that you wish to delete
    *****************************************************************************************/
    public function deleteTemplate($id)
    {
        $url      = $this->url.'/api/deletetemplate.json'; 
        $params   = array('id'=>$id);
        $params   = array_merge($params,$this->getAuthParams()); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;    
    }

    /*****************************************************************************************
    * used to check balance * * * * * * * * *  * * * * * * * * * * * * * * * * * * * * * * * 
    *****************************************************************************************/
    public function balanceCheck()
    {
        $url      = $this->url.'/api/creditstatus.json';
         $client  = new Client();
        $response = $client->request('POST', $url, ['query' => $this->getAuthParams(), 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to update profile
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * fname(mandatory)    - first name of user
    * lname(mandatory)    - last name of user
    * number(mandatory)   - mobile number of user
    * emailid(mandatory)  - email id of user
    *****************************************************************************************/
    public function updateProfile($fname,$lname,$number,$emailid)
    {
        $url      = $this->url.'/api/updateprofile.json';
        $params   = array('firstname'=>$fname,'lastname'=>$lname,
        			'mobilenumber'=>$this->formatNumber($number),'emailid'=>$emailid);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body; 
    }

    /*****************************************************************************************
    * used to generate otp
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * mobileno(mandatory)    - valid mobile number including country code without leading 0 or + symbol
    *                          multiple numbers can be sent seperated by comma(,)
    * template(mandatory)    - Template to be used for sending OTP, it is mandatory to include [otp] tag in 
                               template content.
    *****************************************************************************************/
    public function generateOtp($mobileno,$template)
    {
        $url 	  = $this->url.'/api/mverify.json';
        $params   = array('sender'=>$this->sender,'mobileno'=>$this->formatNumber($mobileno),'template'=>$template);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body 	  = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to validate otp
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * mobileno(mandatory)    - valid mobile number including country code without leading 0 or + symbol
    *                          multiple numbers can be sent seperated by comma(,)
    * otp(mandatory)         - OTP entered by the user
    *****************************************************************************************/
    public function validateOtp($mobileno,$otp)
    {
        $url      = $this->url.'/api/mverify.json';
        $params   = array('code'=>$otp,'mobileno'=>$this->formatNumber($mobileno));
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to create short url
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * url(mandatory)    - long url that you wish to shorten
    *****************************************************************************************/
    public function createShortUrl($longurl)
    {
        $url      = $this->url.'/api/createshorturl.json';
        $params   = array('url'=>$longurl);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * used to delete short url
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * Parameters accepted
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    * url(mandatory)    - short url id that you wish to delete
    *****************************************************************************************/
    public function deleteShortUrl($urlid)
    {
        $url      = $this->url.'/api/deleteshorturl.json';
        $params   = array('id'=>$urlid);
        $params   = array_merge($params,$this->getAuthParams());
        $client   = new Client();
        $response = $client->request('POST', $url, ['query' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }
	
	/*****************************************************************************************
    * get countries list
    *****************************************************************************************/
    public function getCountries()
    {
        $url      = $this->url.'/api/countrylist.json';
        $client   = new Client();
        $response = $client->request('POST', $url, ['http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }
}
?>