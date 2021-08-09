<?php
/**
 *  @author    Cozy Vision Technologies Pvt. Ltd.
 *  @copyright 2010-2020 Cozy Vision Technologies Pvt. Ltd.
 */
namespace SMSAlert\Lib\Smsalert;

use SMSAlert\Lib\Curl\Client;
/**
 * Smsalert class. 
 */
class Smsalert{
    /**
     * Sender.
     *
     * @var string Declare senderid of user.
     */
    private $sender;      
    /**
     * Route.
     *
     * @var string Declare route of user.
     */
    private $route;          
    /**
     * Url.
     *
     * @var string Define url.
     */
    private $url='http://www.smsalert.co.in';   
    /**
     * Authentication parameters.
     *
     * @var array Define Authparams. 
     */
    private $authParams=array();   
    /**
     * Prefix.
     *
     * @var string Prefix eg.91.
     */
    private $prefix; 
	 /**
     * errors.
     *
     * @var array errors.
     */
    private $errors =  array(); 
	/*****************************************************************************************
    * Used to set apikey.
    * 
    * @param string $apikey - API key.
    *
    * @return object
    *****************************************************************************************/
    function authWithApikey($apikey)
    {
        $this->authParams = array('apikey'=>$apikey);
        return $this;
    }

    /*****************************************************************************************
    * Used to set username and password.
    * 
    * @param string $user - SMS Alert account username.
    * @param string $pwd  - SMS Alert account password.
    *
    * @return object 
    *****************************************************************************************/
    function authWithUserIdPwd($user,$pwd)
    {
        $this->authParams = array('user'=>$user,'pwd'=>$pwd);
        return $this;
    }

    /*****************************************************************************************
    * Used to set route. 
    * 
    * @param string $route - route
    *
    * @return object
    *****************************************************************************************/
    public function setRoute($route)
    {
        $this->route     = $route;
        return $this;
    } 

    /*****************************************************************************************
    * Used to set sender id. 
    * 
    * @param string $sender - Sender ID.
    *
    * @return object
    *****************************************************************************************/
    public function setSender($sender)
    {
        $this->sender    = $sender;
        return $this;
    }
	
    /*****************************************************************************************
    * Internal function for authenticate parameters to be used only for this class. 
    * 
    * @return object  
    *****************************************************************************************/
    private function getAuthParams()
    {
		if(array_search("", $this->authParams) !== false)
        {
			$this->errors[]="Missing parameters : Apikey OR Username & Password";
			return false;
		}else{
            return $this->authParams;
        }
    }
	
	/*****************************************************************************************
    * Internal function for Get Error Messages.
    * 
    * @return array
    *****************************************************************************************/
	private function get_errors()
	{
		if(!empty($this->errors))
		{
			return array('status'=>'error','description'=>$this->errors);
		}
		else{
			return false;
		}
	}
    
    /*****************************************************************************************
    * Internal function for format number to be used only for this class.   
    * 
    * @param string $mobileno - Mobile number
    *
    * @return string
    *****************************************************************************************/
    private function formatNumber($mobileno)
    {    
         $prefix   = $this->prefix;
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
    * Set force prefix to be used only for this class.
    *
    * @param string $prefix - prefix eg.91.
    *
    * @return object
    *****************************************************************************************/
    public function setForcePrefix($prefix="")
    {    
       $this->prefix    = $prefix;
       return $this;
    }
    
    /*****************************************************************************************
    * Sending or scheduling sms.
   
    * @param string $mobileno  - (mandatory) Valid mobile number including country code without leading 0 or + symbol
    *                             multiple numbers can be sent seperated by comma(,).
    * @param string $text      - (mandatory) SMS content to be sent without encoded. 
    * @param string $schedule  - (optional) Schedule your messages eg : '2020-06-10 12:00:02'.
    * @param string $reference - (optional) Reference no. for delivered msg report eg : '125546'.
    * @param string $dlrurl    - (optional) Callback url for delivery notification(url encoded format) eg : http://www.test.com/dlr.php.
	* @param bool $shortenurl  - (optional) Convert your long url into short url.	
    *
    * @return array
    *****************************************************************************************/
    public function send($mobileno,$text,$schedule=null,$reference=null,$dlrurl=null,$shortenurl=false)
    {   
        if(empty($mobileno))
		{
			$this->errors[]='Mobile number is missing';
		}
		if(empty($text))
		{
			$this->errors[]='sms text is missing';
		}
		
		$url    = $this->url.'/api/push.json';
        $params = array('sender'=>$this->sender,'mobileno'=>$this->formatNumber($mobileno),'text'=>$text);
		
		

		if($shortenurl){$params['shortenurl'] = 1;}		
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
               $this->errors[]='you must use reference parameter to use DLR callback url';
			}   
        }
		$user_auth = $this->getAuthParams();
		if($this->get_errors())
		{
			return $this->get_errors();
		}
		$params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }
    
    /*****************************************************************************************
    * Used to edit schedule sms.
    * 
    * @param int $batchid     - (mandatory) batch id of the scheduled sms, received while sending the sms through API.
    * @param string $schedule - (mandatory) Date and time for updated schedule (Format: YYYY-MM-DD HH:MM:SS)
    *                            eg : '2020-06-10 12:00:02'.
    *
    * @return array
    *****************************************************************************************/
    public function editSchedule($batchid,$schedule)
    {
        $url      = $this->url.'/api/modifyschedule.json';
        $params   = array('batchid'=>$batchid,'schedule'=>$schedule);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
		$params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * Used to cancel schedule sms.
    *
    * @param int $batchid  - (mandatory) batch id of the scheduled sms, received while sending the sms through API.
    *
    * @return array
    *****************************************************************************************/
    public function cancelSchedule($batchid)
    {
        $url 	  = $this->url.'/api/cancelschedule.json';
        $params   = array('batchid'=>$batchid);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
		$params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * Used to send sms report.
    *
    * @param int $limit    - (optional) To get the list of records available per page. Default value for page is 10.
    * @param int $page     - (optional) To get the list of records from the respective pages. Default value for page is 1.
    * @param int $schedule - (optional) Schedule of sms report.
    *
    * @return array
    *****************************************************************************************/
    public function smsReport($limit=10,$page=1,$schedule=1)
    {
        $url      = $this->url.'/api/smscampaignlog.json';
        $params   = array('limit'=>$limit,'page'=>$page,'schedule'=>$schedule);
        $user_auth= $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
		$params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * Used to pull sms report.
    * 
    * @param int $batchid - (mandatory) Batch id received in response to every push request.
    *
    * @return array
    *****************************************************************************************/
    public function pullReport($batchid)
    {   
        $url      = $this->url.'/api/pull.json';
        $params   = array('batchid'=>$batchid);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
		$params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;   
    }

    /*****************************************************************************************
    * Used to retrieve senderid list.
    * 
    * @return array 
    *****************************************************************************************/
    public function getSenderId()
    {
		$user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
		$url      = $this->url.'/api/senderlist.json';
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $this->getAuthParams(),'http_errors'=>false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;  
    }

    /*****************************************************************************************
    * Used to retrieve user profile. 
    * 
    * @return array 
    *****************************************************************************************/
    public function getUserProfile()
    {
		$user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $url      = $this->url.'/api/user.json';
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $this->getAuthParams(), 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to send sms xml push api.
    *
    * @param array $sms_datas  - (mandatory) Array of number and msg to send sms.
                                  eg:array(array('number'=>'8010551055','sms_body'=>'New Messages')).
	* @param bool $shortenurl  - (optional) Convert your long url into short url.
    * 
    * @return array 						  							  
    *****************************************************************************************/
    public  function sendSmsXml($sms_datas,$shortenurl=false,$schedule=null)
	{ 
		if(is_array($sms_datas) && sizeof($sms_datas) == 0)
        {return false;}
$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<message>
</message>
XML;
        $msg  = new \SimpleXMLElement($xmlstr);
        $user = $msg->addChild('user');
        if (array_key_exists("apikey",$this->authParams))
        {
            $user->addAttribute('apikey', $this->authParams['apikey']);  
        }else{
            $user->addAttribute('username', $this->authParams['user']);
            $user->addAttribute('password', $this->authParams['pwd']); 
        } 
		if(!empty($this->route))
		{
			$user->addAttribute('route', $this->route);
		}
		
		if(!empty($schedule))
		{
			$user->addAttribute('schedule', $schedule); 
		}
		
		if($shortenurl){$user->addAttribute('shortenurl', 1);}
		
        foreach($sms_datas as $sms_data){
            $sms     = $msg->addChild('sms');
            $sms->addAttribute('text', $sms_data['sms_body']);
            $address = $sms->addChild('address');
            $address->addAttribute('from', $this->sender);
            $address->addAttribute('to', $this->formatNumber($sms_data['number']));
        }
        if($msg->count() <= 1)
        { return false; }         
        $xmldata  = $msg->asXML();
        $url      = $this->url.'/api/xmlpush.json';
        $params   = array('data'=>$xmldata); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to retrieve group list.
    *
    * @param int $limit    - (optional) To get the list of records available per page. Default value for page is 10
    * @param int $page     - (optional) To get the list of records from the respective pages. Default value for page is 1
    * @param string $order - (optional) To get the list of records in 'desc' order by default.
    * 
    * @return array 
    *****************************************************************************************/
    public function getGroupList($limit=10,$page=1,$order='desc')
    {
        $url      = $this->url.'/api/grouplist.json';
        $params   = array('limit'=>$limit,'page'=>$page,'order'=>$order);
		$user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to create group.
    *  
    * @param string $grpname - (mandatory) group name, that you wish to create.
    * 
    * @return array 
    *****************************************************************************************/
    public function createGroup($grpname)
    {
        $url      = $this->url.'/api/creategroup.json';
        $params   = array('name'=>$grpname);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to delete group.
    *
    * @param int $grpid  - (mandatory) group id, that you wish to delete.
    * 
    * @return array
    *****************************************************************************************/
    public function deleteGroup($grpid)
    {
        $url      = $this->url.'/api/deletegroup.json';
        $params   = array('id'=>$grpid);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to edit group.
    *
    * @param string $grpname - (mandatory) the group name that you want to modified.
    * @param int $grpid      - (mandatory) group id, that you wish to modified.
    * 
    * @return array
     *****************************************************************************************/
    public function editGroup($grpname,$grpid)
    {
        $url      = $this->url.'/api/updategroup.json';
        $params   = array('id'=>$grpid,'name'=>$grpname);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to send group sms.
    *
    * @param int $grpid       - (mandatory) group id, that you wish to send sms.
    * @param string $text     - (mandatory) sms content to be sent without encoded.
    * @param string $schedule - (optional) date and time for schedule sms for group (Format: YYYY-MM-DD HH:MM:SS)
    *                            eg : '2020-06-10 12:00:02'.
    * 
    * @return array
    *****************************************************************************************/
    public function sendGroupSms($grpid,$text,$schedule=null)
    {
        $url             = $this->url.'/api/grouppush.json';
        $params          = array('id'=>$grpid,'text'=>$text,'schedule'=>$schedule,'sender'=>$this->sender);
        $params['route'] = !empty($this->route) ? $this->route : '';
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client          = new Client();
        $response        = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body            = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to retrieve contact list.
    * 
    * @param int $grpid  - (mandatory) group id, to get contact list of group.
    * @param int $limit  - (optional) to get the list of records available per page. Default value for page is 10.
    * @param int $page(   - (optional) to get the list of records from the respective pages. Default value for page is 1.
    * @param string $order   - (optional) to get the list of records in 'desc' order by default.
    * 
    * @return array
    *****************************************************************************************/
    public function getContactList($groupid,$limit=10,$page=1,$order='desc')
    {
        $url      = $this->url.'/api/contactlist.json';
        $params   = array('group_id'=>$groupid,'limit'=>$limit,'page'=>$page,'order'=>$order);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to create contact.
    * 
    * @param string $grpname(mandatory)  - group name in which you want to create contact.
    * @param string $name(mandatory)     - contact name of the person.
    * @param string $number(mandatory)   - contact number of the person.
    * 
    * @return array
    *****************************************************************************************/
    public function createContact($grpname,$name,$number)
    {
        $url      = $this->url.'/api/createcontact.json';
        $params   = array('grpname'=>$grpname,'name'=>$name,'number'=>$this->formatNumber($number));
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to edit contact.
    * 
    * @param int $contactid - (mandatory) contact Number Id.
    * @param string $name      - (mandatory) contact Name of the person.
    * @param string $number    - (mandatory) contact Number of the person.
    * 
    * @return array
    *****************************************************************************************/
    public function editContact($contactid,$name,$number)
    {
        $url      = $this->url.'/api/updatecontact.json';
        $params   = array('id'=>$contactid,'name'=>$name,'number'=>$this->formatNumber($number));
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to delete contact.
    * 
    * @param int $id - (mandatory) contact number id that wish you to delete.
    * 
    * @return array
    *****************************************************************************************/
    public function deleteContact($id)
    {
        $url      = $this->url.'/api/deletecontact.json';
        $params   = array('id'=>$id);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to import contact.
    *
    * @param array $sms_datas - (mandatory) array of number and msg to create contact
    *                            eg:array(array('person_name'=>'Ankit Sharma','number'=>'8999999999')).
    * @param string $grpname  - (mandatory) group name to add contact.
    * 
    * @return array
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
        $msg   = new \SimpleXMLElement($xmlstr);
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
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to retrieve template list. 
    * 
    * @return array 
    *****************************************************************************************/
    public function getTemplateList()
    {
		$user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $url      = $this->url.'/api/templatelist.json';      
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $this->getAuthParams(), 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to create template.
    *
    * @param string $name  - (mandatory) template name.
    * @param string $text  - (mandatory) sms content of template without encoded.
    * 
    * @return array
    *****************************************************************************************/
    public function createTemplate($name,$text)
    {
        $url      = $this->url.'/api/createtemplate.json'; 
        $params   = array('name'=>$name,'text'=>$text);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);     
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }
    
    /*****************************************************************************************
    * Used to edit template.
    *
    * @param string $name  - (mandatory) name of template.
    * @param string $text  - (mandatory) sms content of template without encoded.
    * @param int $id    - (mandatory) template id that you wish to edit.
    * 
    * @return array
    *****************************************************************************************/
    public function editTemplate($name,$text,$id)
    {
        $url      = $this->url.'/api/updatetemplate.json'; 
        $params   = array('name'=>$name,'text'=>$text,'id'=>$id);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth);     
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to delete template.
    *
    * @param int $id - (mandatory) template id that you wish to delete.
    * 
    * @return array
    *****************************************************************************************/
    public function deleteTemplate($id)
    {
        $url      = $this->url.'/api/deletetemplate.json'; 
        $params   = array('id'=>$id);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;    
    }

    /*****************************************************************************************
    * Used to check balance. 
    * 
    * @return array 
    *****************************************************************************************/
    public function balanceCheck()
    {
		$user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $url      = $this->url.'/api/creditstatus.json';
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $this->getAuthParams(), 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to update profile.
    * 
    * @param string $fname    - (mandatory) first name of user.
    * @param string $lname    - (mandatory) last name of user.
    * @param string $number   - (mandatory) mobile number of user.
    * @param string $emailid  - (mandatory) email id of user.
    * 
    * @return array 
    *****************************************************************************************/
    public function updateProfile($fname,$lname,$number,$emailid)
    {
        $url      = $this->url.'/api/updateprofile.json';
        $params   = array('firstname'=>$fname,'lastname'=>$lname,
        			'mobilenumber'=>$this->formatNumber($number),'emailid'=>$emailid);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body; 
    }

    /*****************************************************************************************
    * Used to generate otp.
    *
    * @param string $mobileno - (mandatory)valid mobile number including country code without leading 0 or + symbol
    *                            multiple numbers can be sent seperated by comma(,).
    * @param string $template - (mandatory)Template to be used for sending OTP, it is mandatory to include [otp] tag in 
    *                            template content.
    * 
    * @return array
    *****************************************************************************************/
    public function generateOtp($mobileno,$template)
    {
        $url 	  = $this->url.'/api/mverify.json';
        $params   = array('sender'=>$this->sender,'mobileno'=>$this->formatNumber($mobileno),'template'=>$template);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body 	  = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to validate otp.
    * 
    * @param string $mobileno - (mandatory) valid mobile number including country code without leading 0 or + symbol
    *                            multiple numbers can be sent seperated by comma(,).
    * @param string $otp      - (mandatory) OTP entered by the user.
    * 
    * @return array
    *****************************************************************************************/
    public function validateOtp($mobileno,$otp)
    {
        $url      = $this->url.'/api/mverify.json';
        $params   = array('code'=>$otp,'mobileno'=>$this->formatNumber($mobileno));
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to create short url.
    *
    * @param string $longurl - (mandatory) long url that you wish to shorten.
    * 
    * @return array
    *****************************************************************************************/
    public function createShortUrl($longurl)
    {
        $url      = $this->url.'/api/createshorturl.json';
        $params   = array('url'=>$longurl);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }

    /*****************************************************************************************
    * Used to delete short url.
    *
    * @param string $urlid - (mandatory) short url id that you wish to delete.
    * 
    * @return array
    *****************************************************************************************/
    public function deleteShortUrl($urlid)
    {
        $url      = $this->url.'/api/deleteshorturl.json';
        $params   = array('id'=>$urlid);
        $user_auth = $this->getAuthParams();
		if($this->get_errors()){
			return $this->get_errors();
		}
        $params   = array_merge($params,$user_auth); 
        $client   = new Client();
        $response = $client->request('POST', $url, ['json' => $params, 'http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }
	
	/*****************************************************************************************
    * Get countries list.
    * 
    * @return array
    *****************************************************************************************/
    public function getCountries()
    {
		$user_auth = $this->getAuthParams();
		if($this->get_errors())
		{
			return $this->get_errors();
		}
		$url      = $this->url.'/api/countrylist.json';
        $client   = new Client();
        $response = $client->request('POST', $url, ['http_errors' => false]);
        $body     = json_decode($response->getBody(),TRUE); 
        return $body;
    }
}
?>
