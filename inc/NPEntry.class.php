<?php

class NPArtist {
// this class represents an artist, used in NPTrack
	public $name = ""; // the common name of an artist
	public $mbid = ""; // their musicbrainz ID
	public $lastfm_url = ""; // a last.fm artist page
	public $wiki_url = "";
	public $lastfm_pics = array(); // an array of last.fm pictures
	public $relationships = array(); // links to other sites about this artist
	public $members = array(); // other NPArtist objects of members if the artist is a group
}

class NPTrack {
// This class represents an actual track
	public $artist = ""; 		// NPArtist object for this track
	public $artists = array(); 	// An array of NPArtists
	public $title = ""; 		// the title of this track
	public $duration = 0; 		// duration of the track (in seconds)
	public $cover_art = array(); 	// an array of URLs to cover art for this track
	public $relationships = array();// an array of URLs to external sites about this track (e.g. Amazon, iTunes)
	public $mbid = "";
	public $itunes_url = "";
}

class NPEntry {
// This class represents an actual instance of a track being played
	public $track = null;		// NPTrack object
	public $start_time = 0; // the time the track started
	public $duration = 0; 	// the duration in seconds
	public $show = null;	// the show which the track was played on
	public function __construct()
	{
		$this->track = new NPTrack();
	}
}

?>
