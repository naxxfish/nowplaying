<?php
/*
This file is for sinks to dump their data onto it.
*/
require_once "bootstrap.php";
$tag = (isset($argv[1]) ? $argv[1] : $DEFAULT['sink']);
if (!isset($sinks[$tag]))
{
	echo "$tag is not a defined sink! Sploops!\n";
	exit(1);
}

$sink = $sinks[$tag];
$mysink = $sink['class_name'];
$thissink = new $mysink ( $QUEUES[$tag]['input'] );
echo "Processing {$sink['class_name']}\n";

while(1)
{
	try {
		$thissink->process();
	} catch (Exception $e)
	{
		die("Source borked: ".$e->getMessage());
	}
}

?>
