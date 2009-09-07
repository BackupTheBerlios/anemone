<?php
	interface ITplEngine
	{
		/**
		 * @return array
		 */
		public function getFileExtensions();
		public function parse($tplDir, $tplFile, $arguments);
	}
?>