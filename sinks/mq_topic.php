<?php

class MQSink extends NPSink
{
	function sink_object($object)
	{
		echo "[MessageQueue] ".json_encode($object)."\n";
		$topic = new StompQueue(STOMP_HOST, _MQ_TOPIC_);
		$topic->send_np_entry($object);
		$topic->disconnect();
	}
}

$sinks['mqtopic'] = array(
	"class_name" => "MQSink",
	"author" => "Chris Roberts",
	"email" => "c.roberts@csrfm.com",
	"version" => 0.1,
	"url" => "http:\/\/nowplaying.csrfm.com",
	"queue" => "raw"
);

?>
