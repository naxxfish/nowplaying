<?php
require_once "bootstrap.php";

use \Dandelionmood;

class LastfmTrack extends NPFilter
{
	function filter_object($obj)
	{
		$lastfm = new \LastFm\LastFm( _LASTFM_API_KEY_ , _LASTFM_API_SECRET_);
		$args = array();
		if ($obj->track->mbid)
		{
			echo "We have a mbid: {$obj->track->mbid}\n";
			$args = array(
				"track" => $obj->track->title,
				"artist" => $obj->track->artist,
				"mbid" => $obj->track->mbid,
				"autocorrect" => 1
			);
		} else
		{
			echo "No mbid";
			$args = array(
				"track" => $obj->track->title,
				"artist" => $obj->track->artist,
				"autocorrect" => 1
			);
		}
		try {
			$result = $lastfm->track_getInfo( $args );
			if ($result->track)
			{
				$obj->track->lastfm_url = $result->track->url;
				if ($result->track->album)
				{
					echo "Getting cover art\n";
					foreach ($result->track->album->image as $image)
					{
						$obj->track->cover_art[$image->size] = $image->{'#text'};
					}
				}
				if ($result->track->mbid)
				{
					echo "Filling in missing MBID for track\n";
					if (!$obj->track->mbid)
						$obj->track->mbid = $result->track->mbid;
				}
			}

			foreach ($obj->track->artists as $artist)
			{
				if ($artist->mbid)
				{
					echo "Looking up artist by mbid\n";
					$args = array( "mbid" => $artist->mbid );
				} else {
					echo "Looking up artist by name\n";
					$args = array( "artist" => $artist->name, "autocorrect" => 1, "lang" => "en" );
				}
				try {
					$result = $lastfm->artist_getInfo( $args );
					if ($result->artist)
					{
						echo "LastFM Artist Info\n";
						if ($result->artist->image)
						{
							echo "Attaching artist images\n";
							foreach ($result->artist->image as $image)
							{
								$artist->lastfm_pics[$image->size] = $image->{'#text'};
							}
						}
						if ($result->artist->url)
						{
							echo "Adding LastFM Artist URL\n";
							$artist->lastfm_url = $result->artist->url;
						}
					}
				} catch (Exception $e)
				{
					echo "Couldn't lookup artist: ".$e->getMessage()."\n";
					continue;
				}

			}
		} catch (Exception $e)
		{
			echo "Couldn't lookup track: ".$e->getMessage()."\n";
		}
		echo "Done processing\n";
		return $obj;
	}
}
$filters['lastfm_track'] = array(
	"class_name" => "LastfmTrack",
	"author" => "Chris Roberts"
);
?>
