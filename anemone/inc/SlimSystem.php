<?php
	class SlimSystem extends Observable implements ISettable
	{
		private $http_base_dir;
		private $fs_base_dir;
		
		private $components_directory = "components";
		private $page;
		
		private $template_dir = array();
		private $template_engines = array();
		private $pages = array();
	
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
	
		public function set($varname, $value) {
			if($varname == "default_page")
				$varname = "page";
			$this->$varname = $value;
		}
		
		public function get($varname) {
			if($varname == "default_page")
				$varname = "page";
			return $this->$varname;
		}
		
		public function getAvailableProperties() {
			return array("template_dir", "default_page");
		}
		
		public function getFsBaseDir() {
			return $this->fs_base_dir;
		}
	
		public function getHttpBaseDir() {
			return $this->http_base_dir;
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
								if(is_callable("$assumed_class_name::createInstance", false, $fn)) {
									$c = call_user_func(array($assumed_class_name, 'createInstance'), $this);
									if($c instanceof IObserver)
										$c->setSubject($this);
									$componentIncludedEvents[] = new EventComponentIncludedArguments($this, $components_dir.$file, $assumed_class_name, $c);
									unset($c);
								}
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
			 * After this, register template engines
			 */
			foreach($componentIncludedEvents as $event)
				if($event->class_instance != null && $event->class_instance instanceof ITplEngine)
					$this->registerTemplateEngine($event->class_instance);
				
			/**
			 * Then, register content objects
			 */
			foreach($componentIncludedEvents as $event)
				if($event->class_instance != null && $event->class_instance instanceof IContent)
					$this->registerPage($event->class_instance);
		}
	
			
		public function registerPage(IContent & $content) {
			$pages = $content->getPages();
			for($i = 0; $i < count($pages); $i++)
				$this->pages[$pages[$i]] = & $content;
		}
		
		public function registerTemplateEngine(ITplEngine & $engine) {
			$exts = $engine->getFileExtensions();
			for($i = 0; $i < count($exts); $i++)
				$this->template_engines[$exts[$i]] = & $engine;
		}
		
		public function render() {
			$page = $this->get("page");
			if(isset($this->pages[$page])) {
				$this->notify(Observable::EVENT_RENDER_START, new EventRenderArguments($this, $this->pages[$page]));
				$output = $this->pages[$page]->render();
				$this->notify(Observable::EVENT_RENDER_END, new EventRenderArguments($this, $this->pages[$page]));
				return $output;
			} else {
				$this->notify(Observable::EVENT_RENDER_START, new EventRenderArguments($this, new NullContent()));
				$this->notify(Observable::EVENT_RENDER_END, new EventRenderArguments($this, new NullContent()));
				return "";
			}
		}
		
		/**
		 * @param $ext string
		 * @return ITplEngine
		 */
		public function getTplEngine($ext) {
			if(!array_key_exists($ext, $this->template_engines))
				$ext = "dummy";
			return $this->template_engines[$ext];
		}
	}
?>