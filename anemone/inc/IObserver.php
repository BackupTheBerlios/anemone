<?php
	interface IObserver
	{
		public function setSubject(Observable & $subject);
		public function notify(Observable & $subject, $eventType, IEventArguments $arguments);
	}
?>