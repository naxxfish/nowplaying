<?php
require_once "inc/NPEntry.class.php";

class StompQueue {
	public $uri;
	public $queue;
	private $stomp;
	private $connected = false;

	public function __construct ($uri, $queue)
	{
		$this->uri = $uri;
		$this->queue = $queue;
	}

	public function connect()
	{
		if (!$this->connected)
		{
			$this->stomp = new Stomp($this->uri);
			$this->connected = true;
		}
	}

	public function disconnect()
	{
		unset($this->stomp);
		$this->connected = false;
	}

	public function send($message)
	{
		$this->connect();
		if ($this->stomp)
		{
			return $this->stomp->send($this->queue, json_encode($message));
		}
		return false;
	}

	public function subscribe()
	{
		$this->connect();
		if ($this->stomp)
		{
			return $this->stomp->subscribe($this->queue);
		}
		return false;
	}

	public function read_message($callback)
	{
		$this->connect();
		do {
			$frame = $this->stomp->readFrame();
			if ($frame)
			{
				if ($callback != null)
					$callback(json_decode($frame->body));
				$this->stomp->ack($frame);
			}
		} while(!$frame);
	}

	public function send_np_entry($obj)
	{
		$this->connect();
		return $this->send($obj);
	}

	public function read_np_entry($callback)
	{
		$this->connect();
		$this->_np_entry_cb = $callback;
		$this->read_message(function ($obj)
		{
			$npentry = new NPEntry();
			foreach ($npentry as $key=>$value)
			{
				// skip if the property doesn't exist (preseve defaults)
				if (!property_exists($obj, $key))
					continue;
				switch ($key)
				{
					case ('track'):
						//parse the track object
						$npentry->track = $this->parse_track($obj->track);
						break;
					default:
						$npentry->$key = $obj->$key;
				}
			}
			$mycb = $this->_np_entry_cb;
			$mycb ( $npentry );
		});
	}

	private function parse_track($obj)
	{
		$track = new NPTrack();
		foreach ($obj as $key=>$value)
		{
			if (!property_exists($track, $key))
				continue;
			switch($key)
			{
				case 'artists':
					$track->artists = array();
					foreach ($obj->artists as $artist)
					{
						$track->artists[] = $this->parse_artist($artist);
					}
					break;
				default:
					$track->$key = $obj->$key;
			}
		}
		return $track;
	}

	private function parse_artist($obj)
	{
		$artist = new NPArtist();
		foreach ($obj as $key=>$value)
		{
			if (!property_exists($artist, $key))
				continue;
			$artist->$key = $obj->$key;
		}
		return $artist;
	}

}
?>
