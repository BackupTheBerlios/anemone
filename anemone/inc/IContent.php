<?php
	interface IContent
	{
		public function setParentContent(IContent & $parent_content);
		public function render();
		public function getOutput();
		public function getContentType();
	}
?>