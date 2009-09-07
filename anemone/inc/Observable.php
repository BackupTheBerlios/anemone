<?php
	abstract class Observable
	{
		const EVENT_ALL = 0;
		
		const EVENT_TPL_START = 1;
		const EVENT_TPL_END = 2;
		
		const EVENT_POST = 3;
		
		const EVENT_INIT_START = 4;
		const EVENT_INIT_END = 5;
		
		const EVENT_COMPONENT_INCLUDED = 6;
		
		private $observers;
		
		protected function __construct() {
			$this->observers = array(array());
		}
		
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
