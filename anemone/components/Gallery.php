<?php
	class Gallery extends Settable implements IComponent, IContent
	{
		public $items = array();
		private $imgdir;
		protected $settable_properties = array("image_directory");
		
		public static function createInstance(SlimSystem & $system) {
			$c = __CLASS__;
			return new $c();
		}
		
		function __set($name, $value) {
			if($name == "image_directory") {
				$this->imgdir = $value;
				$this->loadItems($value);
			}
		}
		
		function __get($name) {
			if($name == "image_directory")
				return $this->imgdir;
		}
		
		public function getPages() {
			return array("gallery");
		}
		
		public function loadItems($path) {
			if ($dh = opendir($path)) {
				while (($file = readdir($dh)) !== false) {
					$this->items[] = new Item($file);
				}
				closedir($dh);
			}
		}
		
		public function render(){
			$tpl = "gallery.tal";
			$tpl_engine = SlimSystem::getInstance()->getTplEngine("tal");
			return $tpl_engine->parse("./tpl/", $tpl, $this);
		}
	}
	
	class Item
	{
		public $src;
		function __construct($src) {
			$this->src = $src;
		}
	}
?>