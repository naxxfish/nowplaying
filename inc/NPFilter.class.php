<?php

/* * This is the interface that defines how now playing data is sinked*/
require_once "inc/NPElement.class.php";

abstract class NPFilter extends NPElement
{
	// these functions set the input and output queues
	public function set_input_queue($queue)
	{
		$this->input_queue = $queue;
	}

	public function set_output_queue($queue)
	{
		$this->output_queue = $queue;
	}

	// this function processes one object and returns it
	public abstract function filter_object($obj);

	// implementation that will read a message from the input queue
	// apply filter_object to it then send it to the output queue
	public function process()
	{
		$this->input_queue->read_np_entry(
			function ($npentry) {
				$obj = $this->filter_object($npentry);
				if (!($obj instanceof NPEntry))
				{
					echo "\nShorted out this element because of an error\n";
					$this->output_queue->send_np_entry($npentry);
					return;
				} else {
					$this->output_queue->send_np_entry( $obj );
				}
			}
		);
	}

	public function __construct($inputqueue, $outputqueue)
	{
		$this->input_queue = $inputqueue;
		$this->output_queue = $outputqueue;
		$this->input_queue->subscribe();
		$this->np_object = null;
	}
}

?>
