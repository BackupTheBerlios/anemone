<?php
	class ActualIniFileContent implements IContent, IObserver
	{
		private $loadedSettableClasses = array();
		private $output;
		
		public function setSubject(Observable & $subject) {
			$subject->register($this, Observable::EVENT_COMPONENT_INCLUDED);
		}
		
		public function setParentContent(IContent & $parent_content) {
			
		}
		
		public function getContentType() {
			return "text/plain";
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
			foreach($this->loadedSettableClasses as $classname => $instance) {
				$output .= "[".$classname."]\n";
				foreach($instance->getAvailableProperties() as $property) {
					$value = $instance->getProperty($property);
					if(is_array($value)) {
						foreach($value as $val) {
							$output .= $property."[] = ".$val."\n";
						}
					} else {
						$output .= $property." = ".$instance->$property."\n";
					}
				}
				$output .= "\n";
			}
			$this->output = $output;
			return $this->output;
		}
		
		public function getOutput() {
			return $this->output;
		}
	}
?>