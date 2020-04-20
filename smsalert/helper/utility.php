<?php
include("smsalert/helper/guzzle/vendor/autoload.php");
use GuzzleHttp\Client;
class Utility 
{	
	/*****************************************************************************************
    * used to invoke Api
    *
    * Parameters accepted
    *
    * url(mandatory)      - api url to hit
    * params(mandatory)   - query string parameters
    * method(optional)	  - requeset method to invoke api
    *****************************************************************************************/
	public static function invoke_Api($url,$params,$method='POST')
	{
		$client = new Client();
		$response = $client->request($method, $url, ['query' => $params]);
		$body = json_decode($response->getBody(),TRUE); 
		return $body;
	}
}
?>
