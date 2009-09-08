<?php
	class PHPTALEngine implements ISettable, ITplEngine, IComponent
	{
		// settable variables
		public $phptal_include_path = "./lib";
		public $tpl_file_extensions = array("tal");
		
		private $is_included = false;
		
		private function include_php_tal_file() {
			$previous_include_path = ini_get("include_path");
			ini_set("include_path", $this->phptal_include_path.PATH_SEPARATOR.$previous_include_path);
			require_once("PHPTAL.php");
			ini_set("include_path", $previous_include_path);
			$this->is_included = true;
		}
		
		public function getFileExtensions() {
			return $this->tpl_file_extensions;
		}
		
		public function getFsBaseDir() {
			return $this->fs_base_dir;
		}
		
		public function getHttpBaseDir() {
			return $this->http_base_dir;
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