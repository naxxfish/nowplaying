<?php

use Guzzle\Http\Client;

class iTunesMatcher extends NPFilter
{
	public function filter_object($obj)
	{
		echo "Searching iTunes URL for {$obj->track->title} - {$obj->track->artists[0]->name}\n";
		$client = new Client( _ITUNES_SEARCH_BASE_ );
		$request = $client->get("?term={$obj->track->title} {$obj->track->artists[0]->name}");
		try {
			$response = $request->send();
			$records = $response->json();
		} catch (Exception $e)
		{
			echo "Couldn't lookup iTunes URL: ".$e->getMessage()."\n";
			return false;
		}
		if (!$records || $records['resultCount'] < 1)
		{
			echo "No records matched, stopping processing here\n";
			return false;
		}
		$result = $records['results'][0];
		echo "Found iTunes URL{$result['trackViewUrl']}\n";
		$obj->track->itunes_url = $result['trackViewUrl'];
		echo "Done processing\n";
		return $obj;
	}
}
$filters['itunes_matcher'] = array (
	"class_name" => "iTunesMatcher",
	"author" => "Chris Roberts"
);
?>
