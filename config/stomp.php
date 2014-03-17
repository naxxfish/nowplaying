<?php
require_once "inc/StompQueue.class.php";

define('STOMP_HOST',"tcp://localhost:61613");
$QUEUES = array(
	"ocp" => array(
		"output" => new StompQueue(STOMP_HOST, "/nowplaying/raw")
	),
	"mb_track" => array(
		"input" => new StompQueue(STOMP_HOST, "/nowplaying/raw"),
		"output" => new StompQueue(STOMP_HOST, "/nowplaying/mb_track_linkd")
	),
	"lastfm_track" => array(
		"input" => new StompQueue(STOMP_HOST, "/nowplaying/mb_track_linkd"),
		"output" => new StompQueue(STOMP_HOST, "/nowplaying/lastfm_linkd")
	),
	"itunes_matcher" => array(
		"input" => new StompQueue(STOMP_HOST, "/nowplaying/lastfm_linkd"),
		"output" => new StompQueue(STOMP_HOST, "/nowplaying/itunes_matched")
	),
	"public" => array(
		"input" => new StompQueue(STOMP_HOST, "/nowplaying/itunes_matched")
	)
);
?>
