<?php
	interface IContent
	{
		public function getPages();
		public function render();
		public function setParent(IContent & $parent_content);
		public function getParent();
	}
?>