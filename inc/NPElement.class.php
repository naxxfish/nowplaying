<?php

abstract class NPElement {
	// processes an element
	public abstract function process();
	function set_error($error)
	{
		$this->error = $error;
	}

	function get_error()
	{
		return $this->error;
	}
}

?>
