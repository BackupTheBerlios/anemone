<?php
	class SlimSystem extends Observable implements ISettable
	{
		/**
		 * @var string
		 */
		public $root_content;

		/**
		 * @var string
		 */
		private $components_directory = "components";
		
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
		private $output;
	
		private $content_objects = array();
		
		/**
		 * @var SlimSystem
		 */
		private static $instance;
		
		/**
		 * @return SlimSystem
		 */
		public static function getInstance() {
	        if (!isset(self::$instance)) {
	            $c = __CLASS__;
	            self::$instance = new $c();
	        }
	        return self::$instance;
		}
		
		private function __construct() {
			$basedir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
			if(substr($basedir, -1) != "/")
				$basedir .= "/";
			$this->http_base_dir = $basedir;
			
			$basedir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_FILENAME']));
			if(substr($basedir, -1) != "/")
				$basedir .= "/";
			$this->fs_base_dir = $basedir;
			
			if(isset($_GET))
				$this->notify(Observable::EVENT_GET, new EventGetArguments());
			if(isset($_POST))
				$this->notify(Observable::EVENT_POST, new EventPostArguments());
		}
		
		public function getFsBaseDir() {
			return $this->fs_base_dir;
		}
		
		public function getHttpBaseDir() {
			return $this->http_base_dir;
		}
		
		public function registerContent($name, IContent & $content) {
			$this->content_objects[$name] = & $content;
		}
		
		public function loadDefaultComponents() {
			$components_dir = $this->fs_base_dir.$this->components_directory."/";
			$componentIncludedEvents = array();
			
			/**
			 * self initialization fires component loading event, too. This class is
			 * the root component. 
			 */
			$componentIncludedEvents[] = new EventComponentIncludedArguments($this, __FILE__, __CLASS__, $this);
			
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
			            		if($_c instanceof IObserver)
			            			$_c->setSubject($this);
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
			 * Then, register content objects
			 */
			foreach($componentIncludedEvents as $event)
				if($event->class_instance != null && $event->class_instance instanceof IContent)
					$this->registerContent($event->classname, $event->class_instance);
		}
		
		public function render() {
			if(isset($this->content_objects[$this->root_content])) {
				$this->notify(Observable::EVENT_RENDER_START, new EventRenderArguments($this, $this->content_objects[$this->root_content]));
				$this->content_objects[$this->root_content]->render();
				if($this->content_objects[$this->root_content]->getContentType() != "")
					header("Content-Type: ".$this->content_objects[$this->root_content]->getContentType());
				$this->output = $this->content_objects[$this->root_content]->getOutput();
				$this->notify(Observable::EVENT_RENDER_END, new EventRenderArguments($this, $this->content_objects[$this->root_content]));
				return $this->output;
			} else {
				$this->notify(Observable::EVENT_RENDER_START, new EventRenderArguments($this, null));
				$this->notify(Observable::EVENT_RENDER_END, new EventRenderArguments($this, null));
				return "";
			}
		}
		
		public function getOutput() {
			return $this->output;
		}
		
		function debug() {
			var_dump($this->http_base_dir);
			var_dump($this->fs_base_dir);
			print_r($_SERVER);
		}
	}
?>