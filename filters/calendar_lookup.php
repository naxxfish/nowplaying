<?php

/*
DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT DRAFT 
This currently does not work!!!!

We need to modify this so that it works with the Composer package client library
https://developers.google.com/api-client-library/php/
*/
class GCalLookup extends NPFilter
{
	 // OLD function that used to work (once!)
	 /*
	function get_show_name()
	{
		$apiClient = new apiClient();
		$apiClient->setUseObjects(true);
		$service = new apiCalendarService($apiClient);
		$optParams = array( 'timeMin'=>date(DateTime::RFC3339 ,mktime(0,0,0)),'timeMax'=>date(DateTime::RFC3339 ,mktime(23,59,59)));

		$events = $service->events->listEvents( 'csrfm.com_26ectpiaoluupmkavfpk0rlkg4@group.calendar.google.com',$optParams);

		while(true) {
		  foreach ($events->getItems() as $event) {

			$this_hour = (date('G') != 0) ? date('G') : 24;
			$start_hour = date('G',strtotime($event->start->dateTime));
			$end_hour = date('G',strtotime($event->end->dateTime));
			echo "Show: {$event->summary} \t Start Hour: {$event->start->dateTime}; End Hour: {$event->end->dateTime}\r\n<br/>";		
			$start_hour = ($start_hour == 0) ? 24 : $start_hour;
			$end_hour = ($end_hour == 0) ? 24 : $end_hour;

			if (($start_hour <= $this_hour) && ($end_hour > $this_hour))
			{
				return $event->summary;
			}	
		  }
		  $pageToken = $events->getNextPageToken();
		  if ($pageToken) {
			$optParams = array('pageToken' => $pageToken);
			$events = $service->events->listEvents('csrfm.com_26ectpiaoluupmkavfpk0rlkg4@group.calendar.google.com', $optParams);
		  } else {
			break;
		  }
		}

		return "Alternative Shuffle";
	}*/
	
	// New version that uses the new Google API (gettable using Composer :) )
	private function get_show()
	{
		$client = new Google_Client();
		$client->setApplicationName("CSRNowPlaying");
		$client->setDeveloperKey(_GOOGLE_API_KEY_);
		$service = new Google_Calendar($client);
		$now = new DateTime();
		$previously = $now->sub( DateInterval::createFromDateString('1 day') ); // 24 hours ago
		$next 		= $now->add( DateInterval::createFromDateString('1 day') ); // 24 hours in the future
		
		$optParams = array(
			"timeMax" => $next->format(DateTime::RFC3339),
			"timeMin" => $previously->format(DateTime::RFC3339),
			"fields" => "description,items(description,end,endTimeUnspecified,etag,location,originalStartTime,recurringEventId,sequence,start,summary),summary,timeZone)",
			"singleEvents" => true // do this so Google does all the recurring nonsense automatically for us :)
		);
		$events = $service->events->listEvents(_GOOGLE_CALENDAR_ID_, $optParams);
		foreach ($events as $event)
		{
			$start = DateTime::createFromFormat(DateTime::RFC3339, $event['start']['dateTime']);
			$end = DateTime::createFromFormat(DateTime::RFC3339, $event['end']['dateTime']);
			// if the show is on now...
			if ($start < $now && $end > $now)
			{
				// return the event
				return $event;
			}
		}
		return null;
	}
	public function filter_object($obj)
	{
		$event = $this->get_show();
		if ($event)
		{
			$obj->show = array();
			$obj->show['name'] = $event['summary'];
			$obj->show['location'] = $event['location'];
		}
		// Otherwise, there is no show! 
		return $obj;
	}
}
$filters['google_calendar'] = array (
	"class_name" => "GoogleCalendarLookup",
	"author" => "Chris Roberts"
);
?>
