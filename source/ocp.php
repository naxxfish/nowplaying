<?
require_once "inc/NPSource.class.php";
require_once "inc/NPEntry.class.php";
class OCPSource extends NPSource
{
	public function process_request($data)
	{
		$line = (isset($this->data['song']) ? $this->data['song'] : false);
		if (!$line)
			return false;
		$parts = explode(' - ', $line, 2);
		if (count($parts) < 2)
			return false;
		// fairly simplistic, I know, but it's a start!
		$np_obj = new NPEntry();
		$np_obj->track->artist = $parts[1];
		$np_obj->track->title = $parts[0];
		return $np_obj;
	}
}

$sources['ocp'] = array(
	"class_name" => "OCPSource",
	"tag" => "ocp",
	"author" => "Chris Roberts",
	"email" => "c.roberts@csrfm.com",
	"version" => 0.1,
	"url" => "http:\/\/nowplaying.csrfm.com",
	"queue" => "raw"
);

?>
