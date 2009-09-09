<?php
	class Settings implements IObserver, IComponent, ISettable
	{
		public $config_dir;
		
		/**
		 * @var array
		 */
		private $ini_properties = array();
		
		/**
		 * @var Observable
		 */
		private $subject;
		
		public function __construct(SlimSystem & $system) {
		}
		
		public function setSubject(Observable & $subject) {
			$this->subject = & $subject;
			$this->subject->register($this, Observable::EVENT_COMPONENT_INCLUDED);
			$this->loadSettingsFromDirectory(SlimSystem::getInstance()->getFsBaseDir());
			$this->notify($subject, Observable::EVENT_COMPONENT_INCLUDED,
				new EventComponentIncludedArguments($subject, "", get_class($subject), $subject));
		}
		
		public function loadSettingsFromDirectories($directory) {
			if(!is_array($directory)) {
				$this->loadSettingsFromDirectory($directory);
				return;
			}
			foreach($directory as $dir)
				$this->loadSettingsFromDirectory($dir);
		}
		
		public function loadSettingsFromDirectory($directory) {
			if (is_dir($directory)) {
		    	if ($dh = opendir($directory)) {
			        while (($file = readdir($dh)) !== false) {
			        	if(is_file($directory.$file) && substr($file, -4) == ".ini") {
			        		$this->ini_properties = array_merge_recursive($this->ini_properties, parse_ini_file($directory.$file, true));
			        	}
			        }
			        closedir($dh);
			        
		    		if(isset($this->ini_properties[__CLASS__])) {
						$this->applyPropertiesToObject($this, $this->ini_properties[__CLASS__]);
					}
		    	}
			}
		}
		
		public function applyPropertiesToObject(ISettable & $settable, $properties) {
			foreach($properties as $key => $value)
				$settable->set($key, $value);
		}
		
		public function set($key, $value) {
			if(in_array($key, $this->getAvailableProperties())) {
				if($key == "config_dir") {
					$this->setConfigDir($value);
				} else {
					$this->$key = $value;
				}
			}
		}
		
		/**
		 * @param $value
		 * @return array
		 */
		private function setConfigDir($value) {
			$key = "config_dir";
			if(is_array($value)){
				$config_dir = $this->get($key);
				if(!is_array($config_dir))
					$config_dir = array($config_dir);
				$diff_array = array_diff($value, $config_dir);
				if(count($diff_array) == 0)
					return;
				$this->$key = $diff_array;
				$this->loadSettingsFromDirectories($diff_array);
			} else {
				if(in_array($value, $config_dir)) {
					return;
				}
				$diff_array = array($value);
				$this->$key = $diff_array;
				$this->loadSettingsFromDirectory($value);
			}
		}
		
		public function get($varname) {
			if(in_array($varname, $this->getAvailableProperties()))
				return $this->$varname;
		}
		
		public function getAvailableProperties() {
			return array("config_dir");
		}
		
		public function notify(Observable & $subject, $eventType, IEventArguments $arguments) {
			if(! ($arguments instanceof EventComponentIncludedArguments))
				return;

			/**
			 * @var EventIncludeComponentArguments
			 */
			$evArgs = $arguments;
			if($evArgs->class_instance == null || !($evArgs->class_instance instanceof ISettable))
				return;
			
			/**
			 * @var ISettable
			 */
			$settable = $evArgs->class_instance;
			$classname = get_class($settable);
			if($classname == __CLASS__)
				return;
			if(isset($this->ini_properties[$classname]))
				$this->applyPropertiesToObject($settable, $this->ini_properties[$classname]);
		}
	}
?>