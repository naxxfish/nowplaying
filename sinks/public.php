<?php
require_once "inc/NPSink.class.php";

class PublicSink extends NPSink
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
		$fp = fopen("nowplaying.json","w");
		fwrite($fp, json_encode($tidy));
		echo "Wrote to nowplaying.json\n";
		fclose($fp);
	}
}

$sinks['public'] = array(
	"class_name" => "PublicSink",
	"tag" => "public",
	"author" => "Chris Roberts",
	"email" => "c.roberts@csrfm.com",
	"version" => 0.1,
	"url" => "http:\/\/nowplaying.csrfm.com"
);

?>
