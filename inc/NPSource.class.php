<?php
/*
* This is the interface that defines a source of now playing information
*
* HTTP requests are pumped in, and you get a NPRaw out (ready to be processed later)
*/
require_once "inc/NPElement.class.php";
require_once "inc/NPEntry.class.php";

abstract class NPSource extends NPElement
{
	// Takes in some data (an array probably) and turns it into a NPEntry object
	public abstract function process_request($data);

	// data is input into the object here
	public function input_data($data)
	{
		$this->data = $data;
	}

	public function process()
	{
		$obj = $this->process_request($this->data);
		if ($obj instanceof NPEntry)
			return $this->queue->send_np_entry($obj);
		return false;
	}


	public function __construct($queue)
	{
		$this->queue = $queue;
		$this->np_raw_obj = null;
	}
}

?>
