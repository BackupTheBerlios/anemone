<?php
	class DummyTplEngine implements ITplEngine, IComponent
	{
		public static function createInstance(SlimSystem & $system) {
			$c = __CLASS__;
			return new $c();
		}
		
		public function getFileExtensions() {
			return array("dummy");
		}
		
		public function parse($tplDir, $tplFile, $args) {
			return nl2br(htmlentities(file_get_contents($tplDir.$tplFile)));
		}
	}
?>