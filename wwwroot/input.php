<?
/*
This file is for sources to dump their data onto it.
*/
require_once "../bootstrap.php";
$tag = (isset($_GET['source']) ? $_GET['source'] : $DEFAULT['source']) ;
$source = $sources[$tag];
$thissource = new $source['class_name'] ( $QUEUES[$tag]['output'] );
$thissource->input_data($_GET);
if ($thissource->process())
{
	echo "Sorted";
} else {
	echo "BLARGH";
}



?>
