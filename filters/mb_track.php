<?php
require_once "bootstrap.php";

use MusicBrainz\MusicBrainz;
use MusicBrainz\Filters\RecordingFilter;
use MusicBrainz\Filters\ArtistFilter;
use Guzzle\Http\Client;

class MusicBrainzTrack extends NPFilter
{
	function filter_object($obj)
	{
		$brainz = new MusicBrainz(new Client());
		$brainz->setUserAgent('NowPlaying','0.1','http://nowplaying.csrfm.com/');


		try {
			$args = array("artist" => $obj->track->artist);
			echo "MB preliminary Search for {$obj->track->artist}\n";
			$i_artists = $brainz->search(new ArtistFilter($args),1);
		} catch (Exception $e)
		{
			echo "Couldn't search for artist {$obj->track->artist}: ".$e->getMessage();
			echo "Copying artist to the NPEntry->track->artists list\n";
			$obj->track->artists[0] = new NPArtist();
			$obj->track->artists[0]->name = $obj->track->artist;
		}

		$args = array("recording" => $obj->track->title);

		if ($i_artists)
		{
			$i_artist = array_shift($i_artists);
			// Always look for the name of the track
			if ($i_artist)
			{	// use the artist mbid if we have it
				echo "Found initial artist: {$i_artist->name} :: {$i_artist->id}\n";
				$args["arid"] = $i_artist->id;
			} else {
				echo "Couldn't find artist {$obj->track->artist}\n";
			}
		}
		try {
			echo "MB Search for {$args['recording']}".(isset($args['arid']) ? " + {$args['arid']}" : "") ."\n";
			$tracks = $brainz->search(new RecordingFilter($args),5);
		} catch (Exception $e)
		{
			echo "Couldn't search for {$args['recording']}".(isset($args['arid']) ? " + {$args['arid']}" : "")." : ".$e->getMessage()."\n";
		}
		$track = array_shift($tracks);
		$track_mbid = null;
		if ($track)
		{
			echo "Track found: {$track->title} :: {$track->id}\n";
			// might want to make this a bit more relaxed
			if (strtoupper($track->title) == strtoupper($obj->track->title))
			{
				echo "Track matches {$track->title} :: {$track->id}\n";
				$track_mbid = (property_exists($track,'id') ? $track->id : null);
				$obj->track->mbid = $track_mbid;
			}
		}
		if ($track_mbid)
		{ // if we have a mbid for the track
			try {
				usleep(500000); // wait half a second
				echo "MB track lookup using track_mbid '$track_mbid'\n";
				$track = $brainz->lookup('recording', $track_mbid, array('artists'));
				if ($track)
				{
					echo "Found matching track: {$track['title']} :: {$track['id']}\n";
					$obj->track->title = $track['title'];
					$obj->track->mbid = $track['id'];
					$artists = $track['artist-credit'];
				}
			} catch (Exception $e)
			{
				echo "Failed to look up track '{$track['title']}' : ".$e->getMessage()."\n";
			}
		} else {
			echo "No Track ID, so not looking up\n";
			$artists = array(
				array(
					"name" => $i_artist->name,
					"artist" => array ( "id" => $i_artist->id )
				)
			);
		}

		foreach ($artists as $key=>$artist)
		{
			echo "Looking up artist {$artist['name']} :: {$artist['artist']['id']}\n";
			$newartist = new NPArtist();
			$newartist->name = $artist['name'];
			$newartist->mbid = $artist['artist']['id'];
			// place the object in the array first (we'll add stuff to it as we go)
			$obj->track->artists[] = $newartist;
			try {
				usleep(500000);

				echo "MB Artist Lookup for {$newartist->name} :: {$newartist->mbid}\n";
				$artist_lookup = $brainz->lookup('artist', $newartist->mbid, array('url-rels'));
			} catch (Exception $e)
			{
				echo "Failed to lookup artist {$newartist->name} :: {$newartist->mbid}: ".$e->getMessage()."\n";
				continue;
			}
			foreach ($artist_lookup['relations'] as $relation)
			{
				switch ($relation['type'])
				{
					case 'wikipedia':
						$newartist->wiki_url = $relation['url']['resource'];
						break;
					case 'social network':
						$newartist->relationships[] = $relation['url']['resource'];
						break;
				}
			}
		}
		echo "Finished processing '{$obj->track->title}'\n";
		return $obj;
	}
}
$filters['mb_track'] = array(
	"class_name" => "MusicBrainzTrack",
	"author" => "Chris Roberts"
);
?>
