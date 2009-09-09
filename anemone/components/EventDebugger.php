<?php
	class EventDebugger implements IObserver, IContent, IComponent
	{
		private $output = "";
		
		public static function createInstance(SlimSystem & $system) {
			$c = __CLASS__;
			return new $c();
		}
		
		public function getPages() {
			return array();
		}
		
		public function setSubject(Observable & $subject) {
			$this->debug("\n".__CLASS__." constructed by ".get_class($subject));
			$subject->register($this, Observable::EVENT_ALL);
		}
		
		public function notify(Observable & $subject, $eventType, IEventArguments $arguments) {
			$this->debug("Event fired by ".get_class($subject)." with arguments ".get_class($arguments));
			$this->debug($arguments);
		}
		
		private function debug($obj) {
			$str = $this->get_string_representation($obj);
			// echo $str."\n";
			$this->output .= $str."\n";
		}
		
		private function get_string_representation($obj, $level = 0) {
			$str = "";
			if(is_object($obj)) {
				$str .= get_class($obj).":\n";
				foreach(get_object_vars($obj) as $k => $v) {
					if($obj == $this && $k == "output")
						continue;
					$str .= str_repeat("\t", $level+1)."$k->".$this->get_string_representation($v, $level+1)."\n";
				}
				if($obj instanceof ISettable) {
					$ar = $obj->getAvailableProperties();
					if(!is_array($ar)) {
						$str .= str_repeat("\t", $level+1)."No settable attributes!";
					} else {
						foreach($obj->getAvailableProperties() as $k) {
							$str .= str_repeat("\t", $level+1)."$k->".$this->get_string_representation($obj->get($k), $level+1)."\n";
						}
					}
				}
				return $str;
			}
			
			if(is_array($obj)) {
				// TODO:
				// return @implode(", ", $obj);
			}
			
			return $obj;
		}
		
		public function render(){
			return $this->output;
		}
	}
?>