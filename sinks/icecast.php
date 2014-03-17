<?php
require_once "inc/NPSink.class.php";

/*
DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT 
		This may not work!!!
DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT 
*/
class IcecastSink extends NPSink
{
	function sink_object($object)
	{
		// <config> stuff (should be in the config file somewhere
		$host = _ICECAST_HOST_; 						
		$icecastPassword = _ICECAST_PASSWORD_;
		// </config>
		$song = $object->track->title;
		$artist = $object->track->artist;
		// Should probably make this format configurable... 
		$line = "$song by $artist";
		foreach (_ICECAST_MOUNTS_ as $mount)
		{
			// TODO: should do this with cURL 
			file_get_contents("http://admin:{$icecastPassword}@$host/admin/metadata?mount=$mount&mode=updinfo&song=".urlencode($line) );
		}
	}
}

$sinks['icecast'] = array(
	"class_name" => "IcecastSink",
	"tag" => "public",
	"author" => "Chris Roberts",
	"email" => "c.roberts@csrfm.com",
	"version" => 0.1,
	"url" => "http:\/\/nowplaying.csrfm.com"
);

?>
