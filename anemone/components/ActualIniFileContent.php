<?php
	class ActualIniFileContent implements IContent, IObserver, IComponent
	{
		private $loadedSettableClasses = array();
		
		public static function createInstance(SlimSystem & $system) {
			$c = __CLASS__;
			return new $c();
		}
		
		public function setSubject(Observable & $subject) {
			$subject->register($this, Observable::EVENT_COMPONENT_INCLUDED);
		}
		
		public function getPages() {
			return array("systemsettings");
		}
		
		public function notify(Observable & $subject, $eventType, IEventArguments $arguments) {
			/**
			 * @var EventComponentIncludedArguments
			 */
			$c = $arguments->class_instance;
			if($c instanceof ISettable) {
				$this->loadedSettableClasses[get_class($c)] = & $c;
			}
		}
		
		public function render() {
			$output = "";
			echo "render called";
			foreach($this->loadedSettableClasses as $classname => $instance) {
				$output .= "[".$classname."]\n";
				$available_properties = $instance->getAvailableProperties();
				if(!is_array($available_properties) || count($available_properties) == 0) {
					$output .= "; No available properties.\n";
				} else {
					foreach($available_properties as $property) {
						$value = $instance->get($property);
						if(is_array($value)) {
							foreach($value as $val) {
								$output .= $property."[] = ".$val."\n";
							}
						} else {
							$output .= $property." = ".$instance->get($property)."\n";
						}
					}
				}
				$output .= "\n";
			}
			return $output;
		}
	}
?>