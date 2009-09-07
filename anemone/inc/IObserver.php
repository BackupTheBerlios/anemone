<?php
	interface IObserver
	{
		// public function __construct(Observable & $subject);
		public function setSubject(Observable & $subject);
		public function notify(Observable & $subject, $eventType, IEventArguments $arguments);
	}
?>