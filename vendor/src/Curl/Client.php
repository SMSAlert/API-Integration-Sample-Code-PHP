<?php
namespace SMSAlert\Lib\Curl;

Class Client{
	private $response = '';
	private $content = '';
	
	public function request($method="GET",$url=null,$params=array(), $headers=array(), $curl_opts=array())
	{
		$params 		= (!empty($params['json']))?$params['json'] : $params['query'];
		$http_errors    = (!empty($params['http_errors'])) ? $params['http_errors']:false;
		
		if(strtolower($method)!='post')
		{
			$query = (!empty($params)) ? '?'.http_build_query($params) : '';
			$url = $url.$query;
		}
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if(!empty($headers)){
			curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		}
		if(strtolower($method)=='post' && !empty($params)){
			$postfields = (!is_array($params)) ? $params : http_build_query($params);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postfields);
		}
		if(!empty($curl_opts)){
			foreach($curl_opts as $curl_opt => $curl_val){curl_setopt($ch, $curl_opt, $curl_val);}
		}
		$this->response = curl_exec($ch);
		
		if ($http_errors && curl_errno($ch)) { 
			$this->$response = curl_error($ch); 
		} 
		curl_close($ch);
		return new Responses($this->response);
	}
	
}
?>
