<?php
	class PHPTALEngine implements ISettable, ITplEngine, IComponent
	{
		public $phptal_include_path = "./lib";
		public $tpl_file_extensions = array("tal");
		
		private $is_included = false;
		
		public static function createInstance(SlimSystem & $system) {
			$c = __CLASS__;
			return new $c();
		}
		
		private function include_php_tal_file() {
			$previous_include_path = ini_get("include_path");
			ini_set("include_path", $this->get("phptal_include_path").PATH_SEPARATOR.$previous_include_path);
			require_once("PHPTAL.php");
			ini_set("include_path", $previous_include_path);
			$this->is_included = true;
		}
		
		public function set($varname, $value) {
			if(in_array($varname, $this->getAvailableProperties()))
				return $this->$varname = $value;
		}
		
		public function get($varname) {
			if(in_array($varname, $this->getAvailableProperties()))
				return $this->$varname;
		}
		
		public function getAvailableProperties() {
			return array("phptal_include_path", "tpl_file_extensions");
		}
		
		public function getFileExtensions() {
			return $this->get("tpl_file_extensions");
		}
		
		public function parse($tplDir, $tplFile, $args) {
			if(!$this->is_included)
				$this->include_php_tal_file();
			
			/**
			 * @var PHPTAL
			 */
			$tpl = new PHPTAL($tplDir."/".$tplFile);
			$tpl->doc = $args;
			return $tpl->execute();
		}
	}
?>