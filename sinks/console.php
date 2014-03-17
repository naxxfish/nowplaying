<?php
require_once "inc/NPSink.class.php";

class ConsoleSink extends NPSink
{
	private function tidy_object($obj)
	{
		$tidy = array();
		foreach ($obj as $key=>$value)
		{
			if (is_scalar($value))
			{
				if ($value)
					$tidy[$key] = $value;
			} else {
				if (!empty($value))
					$tidy[$key] = $this->tidy_object($value);
			}
		}
		return $tidy;

	}
	function sink_object($object)
	{
		$tidy = $this->tidy_object($object);
		echo "[ConsoleSink] ".json_encode($tidy)."\n";
	}
}

$sinks['console'] = array(
	"class_name" => "ConsoleSink",
	"tag" => "console",
	"author" => "Chris Roberts",
	"email" => "c.roberts@csrfm.com",
	"version" => 0.1,
	"url" => "http:\/\/nowplaying.csrfm.com",
	"queue" => "raw"
);

?>
