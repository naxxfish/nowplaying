<?php

// http://tunein.com/broadcasters/api
/*
DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT 

		This does NOT do anything yet!

DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT 
*/
class TuneInSink extends NPSink
{
	function sink_object($object)
	{
		echo "[MessageQueue] ".json_encode($object)."\n";

	}
}

$sinks['tunein'] = array(
	"class_name" => "TuneInSink",
	"author" => "Chris Roberts",
	"email" => "c.roberts@csrfm.com",
	"version" => 0.1,
	"url" => "http:\/\/nowplaying.csrfm.com",
	"queue" => "raw"
);

?>
