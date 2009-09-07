<?php
	class SlimSystem extends Observable implements ISettable
	{
		/**
		 * @var string
		 */
		private $http_base_dir;
		
		/**
		 * @var string
		 */
		private $fs_base_dir;
		
		/**
		 * @var string
		 */
		public $tpl_dir;
		
		private $template_engines = array();
		
		/**
		 * 
		 * @var SlimSystem
		 */
		private static $instance;
		
		/**
		 * 
		 * @return SlimSystem
		 */
		public static function getInstance() {
	        if (!isset(self::$instance)) {
	            $c = __CLASS__;
	            self::$instance = new $c();
	        }
	        return self::$instance;
		}
		
		protected function __construct() {
			parent::__construct(); 
			$basedir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
			if(substr($basedir, -1) != "/")
				$basedir .= "/";
			$this->http_base_dir = $basedir;
			
			$basedir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_FILENAME']));
			if(substr($basedir, -1) != "/")
				$basedir .= "/";
			$this->fs_base_dir = $basedir;
			
			$this->tpl_dir = $this->fs_base_dir."tpl/";
			
			if(isset($_POST))
				$this->notify(Observable::EVENT_POST, new EventPostArguments());
		}
		
		public function setProperties($array) {
			if(!is_array($array))
				return;
			foreach($array as $key => $value) {
				$this->setProperty($key, $value);
			}
		}
		
		public function setProperty($key, $value) {
			if(in_array($key, $this->getAvailableProperties()))
				$this->$key = $value;
		}
		
		public function getFsBaseDir() {
			return $this->fs_base_dir;
		}
		
		public function getHttpBaseDir() {
			return $this->http_base_dir;
		}
		
		public function getProperty($key) {
			if(in_array($key, $this->getAvailableProperties()))
				return $this->$key;
			return null;
		}
		
		public function getAvailableProperties() {
			return array("tpl_dir");
		}
		
		public function registerTemplateEngine(ITplEngine & $engine) {
			$exts = $engine->getFileExtensions();
			for($i = 0; $i < count($exts); $i++) {
				$this->template_engines[$exts[$i]] = & $engine;
			}
		}
		
		public function initDefaultComponents() {
			$components_dir = $this->fs_base_dir."components/";
			$componentIncludedEvents = array();
			/**
			 * Search php files and include them to catch all the observers
			 */
			if (is_dir($components_dir)) {
		    	if ($dh = opendir($components_dir)) {
			        while (($file = readdir($dh)) !== false) {
			        	if(is_file($components_dir.$file) && substr($file, -4) == ".php") {
			            	include_once $components_dir.$file;
			            	$assumed_class_name = substr($file, 0, -4);
			            	if(class_exists($assumed_class_name)) {
			            		$_c = new $assumed_class_name($this);
			            		$componentIncludedEvents[] = new EventComponentIncludedArguments($this, $components_dir.$file, $assumed_class_name, $_c);
			            		unset($_c);
			            	} else {
			            		$componentIncludedEvents[] = new EventComponentIncludedArguments($this, $components_dir.$file, $assumed_class_name, null);
			            	}
			        	}
			        }
			        closedir($dh);
		    	}
			}
			/**
			 * Go through the components again to fire the notify event for all included components. This is
			 * necessary if the components try to communicate to each other on loading.
			 */
			foreach($componentIncludedEvents as $event)
				$this->notify(self::EVENT_COMPONENT_INCLUDED, $event);
			
			/**
			 * After that, register the template engines.
			 */
			foreach($componentIncludedEvents as $event)
				if($event->class_instance != null && $event->class_instance instanceof ITplEngine)
					$this->registerTemplateEngine($event->class_instance);
		}
		
		public function render($rootTemplate) {
			$ext = substr($rootTemplate, strrpos($rootTemplate, ".")+1);
			$tplEngine = $this->getTplEngine($ext);
			if($tplEngine == null){
				// default: return plain text
				return nl2br(htmlentities(file_get_contents($this->tpl_dir.$rootTemplate)));
			}
			return $tplEngine->parse($this->tpl_dir, $rootTemplate, null);
		}
		
		/**
		 * @param $ext string
		 * @return ITplEngine
		 */
		function getTplEngine($ext) {
			if(!array_key_exists($ext, $this->template_engines))
				return null;
			return $this->template_engines[$ext];
		}
		
		function debug() {
			var_dump($this->http_base_dir);
			var_dump($this->fs_base_dir);
			print_r($_SERVER);
		}
	}
?>