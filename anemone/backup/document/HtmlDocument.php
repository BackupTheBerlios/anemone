<?php
	class HtmlDocument extends Document
	{
		public $head;
		public $body;
		
		public $title = "Anemone";
		
		public function getCssFiles($theme) {
			return array();
		}
		
		public function getJsFiles($theme) {
			return array();
		}
		
		public function getMetadata() {
			return "";
		}
	}
	
	class HtmlElement
	{
		private static $id_number = 0;
		
		public $id;
		public $cssclass = array();
		public $content;
		
		function __construct($id, mixed $content) {
			$this->id = $id;
			$this->content = $content;
		}
	}
?>