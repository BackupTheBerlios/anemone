<?php
	interface ISettable
	{
		public function set($key, $value);
		public function get($key);
		public function getAvailableProperties();
	}
?>