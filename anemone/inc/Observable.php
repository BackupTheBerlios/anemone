<?php
	abstract class Observable
	{
		const EVENT_ALL = 0;
		
		const EVENT_GET = 3;
		const EVENT_POST = 4;
		
		const EVENT_COMPONENT_INCLUDED = 5;
		
		const EVENT_RENDER_START = 6;
		const EVENT_RENDER_END = 7;
		
		private $observers = array(array());
		
		public function register(IObserver & $observer, $eventType) {
			$this->observers[$eventType][] = & $observer;
		}
		
		public function notify($eventType, IEventArguments $context) {
			if($eventType !== Observable::EVENT_ALL)
				$this->notify(Observable::EVENT_ALL, $context);
			if(!isset($this->observers[$eventType]))
				return;
			for($i = 0; $i < count($this->observers[$eventType]); $i++) {
				$this->observers[$eventType][$i]->notify($this, $eventType, $context);
			}
		}
	}
?>
