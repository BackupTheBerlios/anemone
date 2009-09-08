<?php
	class Settings implements IObserver, ISettable, IComponent
	{
		/**
		 * @var array
		 */
		public $config_dir = array();
		
		/**
		 * @var array
		 */
		private $ini_properties;
		
		/**
		 * @var Observable
		 */
		private $subject;
		
		function __construct() {
			$this->ini_properties = array();
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
						$this->setProperties($this, $this->ini_properties[__CLASS__]);
					}
		    	}
			}
		}
		
		public function setProperties(& $settable, $properties) {
			foreach($properties as $key => $value)
				$settable->$key = $value;
		}
		
		// TODO
		public function __set($key, $value) {
			if($key == "config_dir") {
				if(is_array($value)){
					$diff_array = array_diff($value, $this->config_dir);
					if(count($diff_array) == 0)
						return;
					$this->config_dir = array_merge($this->config_dir, $diff_array);
					$this->loadSettingsFromDirectories($diff_array);
				} else {
					if(in_array($value, $this->config_dir)) {
						return;
					}
					$diff_array = array($value);
					$this->config_dir = array_merge($this->config_dir, $diff_array);
					$this->loadSettingsFromDirectory($value);
				}
				return;
			}
		}
		
		public function setProperty($key, $value) {
			if(in_array($key, $this->getAvailableProperties())) {

				$this->$key = $value;
			}
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
				$this->setProperties($settable, $this->ini_properties[$classname]);
		}
	}
?>