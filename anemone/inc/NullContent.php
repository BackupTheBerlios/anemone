<?php
	class NullContent implements IContent
	{
		public function setParentContent(IContent & $parent_content) {}
		public function getContentType() { return ""; }
		public function render() { return ""; }
		public function getOutput() { return $this->render(); }
	}
?>