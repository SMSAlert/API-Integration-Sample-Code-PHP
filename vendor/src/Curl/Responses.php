<?php
namespace SMSAlert\Lib\Curl;
Class Responses{
	private $resp_body = '';
	private $http_code = '';
	
	public function __construct($response, $http_code)
	{
		$this->resp_body=$response;
		$this->http_code=$http_code;
	}
	public function getBody()
	{
		return $this->resp_body;
		
	}
	public function getStatusCode()
	{
		return $this->http_code;
	}
	
}
?>
