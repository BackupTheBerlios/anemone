<?php
	class PHPTALEngine implements ISettable, ITplEngine
	{
		private $phptal_include_path = "./lib";
		private $is_included = false;
		private $tpl_file_extensions = array("tal");
		
		private function include_php_tal_file() {
			$previous_include_path = ini_get("include_path");
			ini_set("include_path", $this->phptal_include_path.PATH_SEPARATOR.$previous_include_path);
			require_once("PHPTAL.php");
			ini_set("include_path", $previous_include_path);
			$this->is_included = true;
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
			return array("phptal_include_path", "tpl_file_extensions");
		}
		
		public function getFileExtensions() {
			return $this->tpl_file_extensions;
		}
		
		public function parse($tplDir, $tplFile, $args) {
			if(!$this->is_included)
				$this->include_php_tal_file();
			
			/**
			 * @var PHPTAL
			 */
			$tpl = new PHPTAL($tplDir."/".$tplFile);
			$tpl->a = $args;
			return $tpl->execute();
		}
	}
?>