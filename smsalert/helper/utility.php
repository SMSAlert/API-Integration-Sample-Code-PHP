<?php
class Utility 
{	
	//function name change invoke_api
	public static function invoke_Api($url,$params)
	{
		$url = (!empty($params)) ?  $url.'?'.http_build_query($params) : $url;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output,true); 
	}
}
?>
