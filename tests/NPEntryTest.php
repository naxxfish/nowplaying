<?php
require_once "inc/NPEntry.class.php";
class NPEntryTest extends PHPUnit_Framework_Testcase
{
	public function testCreate()
	{
		$npentry  = new NPEntry();
		$this->assertInstanceOf('NPEntry', $npentry);
		$this->assertInstanceOf('NPTrack', $npentry->track);
		$this->assertInstanceOf('NPArtist', $npentry->track->artist);
	}

}

?>
