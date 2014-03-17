<?php
/*
This file is for filters to dump their data onto it.
*/
require_once "bootstrap.php";
$tag = (isset($argv[1]) ? $argv[1] : $DEFAULT['filter']);
if (!isset($filters[$tag]))
{
	echo "$tag is not a defined filter! Sploops!\n";
	exit(1);
}

$filter = $filters[$tag];
$myfilter = $filter['class_name'];
$thisfilter = new $myfilter ( $QUEUES[$tag]['input'], $QUEUES[$tag]['output'] );
echo "Processing {$filter['class_name']}\n";
while(1)
{
	try {
		$thisfilter->process();
	} catch (Exception $e)
	{
		die("Source borked: ".$e->getMessage());
	}
}

?>
