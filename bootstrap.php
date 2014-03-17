<?php
require "vendor/autoload.php";
require_once "config/defaults.php";
if (file_exists("config/custom.php"))
{
	require_once "config/custom.php";
}

require_once "config/stomp.php";
require_once "sinks/sink.php";
require_once "source/source.php";
require_once "filters/filter.php";

function __autoload($class_name)
{
	if (file_exists("inc/$class_name.class.php"))
	{
		require_once "inc/$class_name.class.php";
		return
	}
}
?>
