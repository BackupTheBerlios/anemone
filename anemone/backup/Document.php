<?php
	class Document extends Settable implements IContentProvider, IComponent
	{
		private $sections = array();
		private $vars = array();
		private $document;
		
		public $theme = "anemone";
		public $document_class = "HtmlDocument";
		
		protected $settable_properties = array("title", "theme", "document_class");
		
		public static function createInstance(SlimSystem & $system) {
			$document = new Document();
			$doc_class = $document->document_class;
			
			$inc_path = ini_set("include_path", dirname(__FILE__)."/".strtolower(__CLASS__)."/");
			$document->document = new $doc_class();
			ini_set("include_path", $inc_path);
			
			return $document;
		}
		
		public function __set($name, $value) {
			if($name == "title")
				$this->document->title = $value;
		}
		
		public function __get($name) {
			if($name == "title")
				return $this->document->title;
		}
		
		public function addSection($name, IContentProvider $datacontent) {
			$this->sections[$name] = $datacontent;
		}
		
		public function addVariable($name, mixed $obj) {
			$this->vars[$name] = $obj;
		}
		
		public function getSection($name) {
			if(in_array($name, $this->sections))
				return $this->sections[$name];
		}
		
		public function getVariable($name) {
			if(in_array($name, $this->vars))
				return $this->vars[$name];
		}
		
		public function getSectionNames() {
			return array_keys($this->sections);
		}
		
		public function getVariableNames() {
			return array_keys($this->vars);
		}
	}
?>