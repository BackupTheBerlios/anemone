<?php
	class NullContent implements IContent
	{
		public function render() { return ""; }
		public function getPages() {
			return array();
		}
	}
?>