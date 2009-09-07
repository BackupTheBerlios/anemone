<?php
	interface ISettable
	{
		public function setProperties($array);	
		public function setProperty($key, $value);
		public function getProperty($key);
		public function getAvailableProperties();
	}
?>