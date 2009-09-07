<?php
	interface IObserver
	{
	  public function notify(Observable & $subject, $eventType, IEventArguments $arguments);
	}
?>