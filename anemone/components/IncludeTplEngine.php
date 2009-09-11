<?php
	class IncludeTplEngine implements ITplEngine, IComponent
	{
		public static function createInstance(SlimSystem & $system) {
			$c = __CLASS__;
			return new $c();
		}
		
		public function getFileExtensions() {
			return array("php", "phtml", "html", "null");
		}
		
		public function parse($tplDir, $tplFile, $args) {
			ob_start();
			include($tplDir.$tplFile);
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}
	}
?>