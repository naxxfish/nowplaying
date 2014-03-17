<?php
require_once "inc/StompQueue.class.php";
require_once "inc/NPEntry.class.php";

class StompQueueTest extends PHPUnit_Framework_Testcase
{
	public function testCreate()
	{
		$stompqueue = new StompQueue("tcp://localhost:61613", "/nowplaying/testCreate");
		$this->assertTrue($stompqueue->subscribe() );
		$this->assertInstanceOf('StompQueue', $stompqueue);
	}

	public function testSendMessage()
	{
		$stompqueue = new StompQueue("tcp://localhost:61613", "/nowplaying/testSendMessage");
		$this->assertTrue($stompqueue->subscribe() );
		$this->assertTrue($stompqueue->send(array("test"=>"message") )  );
		$stompqueue->read_message(null); // clear out the test message
	}

	public function testSendRecieveRawMessage()
	{
		$stompqueue = new StompQueue("tcp://localhost:61613", "/nowplaying/testSendRecieveRawMessage");
		$this->assertTrue($stompqueue->subscribe() );
		$this->testmessage = new stdClass();
		$this->testmessage->test = "message";
		$this->assertTrue($stompqueue->send($this->testmessage ) );
		$stompqueue->read_message(function ($obj)
		{
			$this->assertEquals($this->testmessage, $obj);
		});
		unset($this->testmessage);
	}

	public function testSendRecieveNPEntry()
	{
		$this->npentry = new NPEntry();
		$this->npentry->track->title = "Testing Track Title";
		$this->npentry->track->artist->name = "Testing Artist";
		$this->npentry->start_time = time();
		$this->npentry->duration = 42;
		$stompqueue = new StompQueue("tcp://localhost:61613", "/nowplaying/testSendRecieveNPEntry");
		$this->assertTrue( $stompqueue->subscribe() );
		$this->assertTrue( $stompqueue->send_np_entry($this->npentry) );
		$stompqueue->read_np_entry(function ($obj)
		{
			$this->assertInstanceOf('NPEntry', $obj);
			$this->assertInstanceOf('NPTrack', $obj->track);
			$this->assertInstanceOf('NPArtist', $obj->track->artist);
			$this->assertEquals($this->npentry->track, $obj->track);
			$this->assertEquals($this->npentry->track->artist, $obj->track->artist);
		});
		unset($this->npentry);
	}
}

?>
