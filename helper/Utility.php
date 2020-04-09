<?php
/**
 * 
 */
class Utility 
{
	
	//function name change invoke_api
	function invoke_api($url,$params)
	{
		$query = (!empty($params)) ? http_build_query($params) : '';
		$url = isset($url) ? $url.$query : '';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output,true); 
	}

}
?>
