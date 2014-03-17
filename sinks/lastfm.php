<?php

/*
DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT 
		This may not work!!!
DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT 
*/

class LastFMSink extends NPSink
{

	function gen_sig ($api_key,$api_secret,$method,$params)
	{
			ksort($params);
			$sig = 'api_key';
			$sig .= $api_key;
			foreach ($params as $key=>$value)
			{
					$sig .= $key.$value;
			}
			$sig .= $api_secret;
			echo "sig: $sig\r\n";
			return md5($sig);
	}


	function sink_object($object)
	{
		// <config> This should be configurable.... 
		$session_key = _LASTFM_SESSION_KEY_; // secret things ahoy ... these should be in the config files.
		$api_key = _LASTFM_API_KEY_;
		$api_secret = _LASTFM_API_SECRET_;
		$url = _LASTFM_URL_;
		// </config>
		
		$method = 'track.updateNowPlaying';

		$params = array(
				'method' => $method,
				'track' => $obj->track->title,
				'artist' => $obj->track->artist,
				'sk' => $session_key
		);
		
		if ($object->track->release_mbid)
		{
		$params['mbid'] = $object->track->release_mbid;
		}
		
		$fields = $params + array (
			'api_key' => $api_key,
			'api_sig' => gen_sig($api_key,$api_secret,$method,$params)
		);
		$fields_string = '';
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&');

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

		//execute post
		$result = curl_exec($ch);
		//echo $result;
		//close connection
		curl_close($ch);
	}
}

$sinks['lastfm'] = array(
	"class_name" => "LastFMSink",
	"tag" => "lastfm",
	"author" => "Chris Roberts",
	"email" => "c.roberts@csrfm.com",
	"version" => 0.1,
	"url" => "http:\/\/nowplaying.csrfm.com"
);

?>
