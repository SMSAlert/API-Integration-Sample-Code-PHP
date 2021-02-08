<?php
namespace SMSAlert\Lib\Curl;
Class Responses{
	private $resp_body = '';
	
	public function __construct($response)
	{
		$this->resp_body=$response;
	}
	public function getBody()
	{
		return $this->resp_body;
		
	}
	
}
?>