<?php
	class Settings implements IObserver, ISettable
	{
		/**
		 * @var array
		 */
		public $config_dir;
		
		/**
		 * @var array
		 */
		private $ini_properties;
		
		/**
		 * @var Observable
		 */
		private $subject;
		
		function __construct(Observable & $subject) {
			$this->ini_properties = array();
			$this->subject = & $subject;
			$this->subject->register($this, Observable::EVENT_COMPONENT_INCLUDED);
			$this->config_dir = array();
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
						$this->setProperties($this->ini_properties[__CLASS__]);
					}
		    	}
			}
		}
		
		public function setProperties($array) {
			if(!is_array($array))
				return;
			foreach($array as $key => $value) {
				$this->setProperty($key, $value);
			}
		}
		
		public function setProperty($key, $value) {
			if(in_array($key, $this->getAvailableProperties())) {
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
				$this->$key = $value;
			}
		}
		
		public function getProperty($key) {
			if(in_array($key, $this->getAvailableProperties()))
				return $this->$key;
			return null;
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
				$settable->setProperties($this->ini_properties[$classname]);
		}
	}
?>