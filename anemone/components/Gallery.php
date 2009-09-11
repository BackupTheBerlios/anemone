<?php
	class Gallery extends Settable implements IComponent, IContent
	{
		public $items = array();
		private $imgdir;
		private $parent_content;
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
					if(is_file($path.$file))
						$this->items[] = new Item($path.$file);
				}
				closedir($dh);
			}
		}
		
		public function render(){
			$tpl = "gallery.tal";
			$tpl_engine = SlimSystem::getInstance()->getTplEngine("tal");
			$out = $tpl_engine->parse("./tpl/", $tpl, $this);
			if($this->getParent()) {
				$this->getParent()->content = $out;
				$out = $this->getParent()->render();
			}
			return $out;
		}
		
		public function setParent(IContent & $parent_content) {
			$this->parent_content = & $parent_content;
		}
		
		public function getParent() {
			return $this->parent_content;
		}
	}
	
	class Item
	{
		public $src;
		public $thumbsrc;
		public $name;
		public $description;
		
		function __construct($src) {
			$this->src = $src;
		}
		
		function createThumbnail() {
			
		}
		
		function fetchIptcInfo() {
			// see flatpress, plugin.bbcode.php
			if (function_exists('iptcparse')) {
				$img_info = array();
				$img_size = @getimagesize($this->src, $img_info);
				if ($img_size['mime'] == 'image/jpeg') {
					// tiffs won't be supported
					if(is_array($img_info)) {   
						$iptc = iptcparse($img_info["APP13"]);
						$title = @$iptc["2#005"][0]? $iptc["2#005"][0] : $this->name;
						$alt = isset($iptc["2#120"][0])? $iptc["2#120"][0] : $this->name;
					}
					$this->name = $title;
					$this->description = $alt;
				}
			}
		}
	}
?>