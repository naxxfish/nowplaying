<?php

/* * This is the interface that defines how now playing data is sinked*/
require_once "inc/NPElement.class.php";

abstract class NPSink extends NPElement
{
	// these functions set the input and output queues
	public function set_input_queue($queue)
	{
		$this->queue = $queue;
	}
	
	// abstract function to sink the object in some manner
	public abstract function sink_object($object);

	// implementation that reads an object then calls sink_object on it
	public function process()
	{
		$this->queue->read_np_entry(function ($npentry) {
			if ($npentry instanceof NPEntry)
				$this->sink_object($npentry);
		});
		return true;
	}

	public function __construct($queue)
	{
		$this->queue = $queue;
		$this->queue->subscribe();
	}
}

?>
