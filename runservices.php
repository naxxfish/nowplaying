<?php
require "bootstrap.php";

$processes = array();
function runprocess($cmd)
{
	$descriptorspec = array(
		0 => array ("pipe", "r"),
		1 => array ("pipe", "w"),
		2 => array ("pipe", "w")
	);
	$handle = proc_open($cmd, $descriptorspec, $pipes);
	stream_set_blocking($pipes[0], 0);
	stream_set_blocking($pipes[1], 0);
	stream_set_blocking($pipes[2], 0);
	if (is_resource($handle))
		return array("handle" => $handle, "pipes" => $pipes);
	die("Couldn't create process");
}
echo "Starting up processes:\n";
foreach ($filters as $filter=>$value)
{
	echo "Started {$filter}\n";
	$processes["filter_{$filter}"] = runprocess(_PHP_BINARY_." bin/runfilter.php {$filter}");
}

foreach ($sinks as $sink=>$value)
{
	echo "Started {$sink}\n";
	$processes["sink_{$sink}"] = runprocess(_PHP_BINARY_." bin/runsink.php {$sink}");
}

while(1)
{
	foreach ($processes as $key => $process)
	{
		$line = fgets($process['pipes'][1]);
		if ($line != "")
		{
			$dt = new DateTime();
			$now = $dt->format("Y-m-d H:i:s");
			$prefix = "[{$now}] {{$key}}";
			if (strlen($prefix) < 45)
				$prefix .= str_repeat(" ", 45-strlen($prefix));
			echo "{$prefix} :: {$line}";
		}
	}
	usleep(10000);
}

?>
