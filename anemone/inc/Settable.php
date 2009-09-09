<?php
	abstract class Settable implements ISettable
	{
		public function set($varname, $value) {
			if(in_array($varname, $this->getAvailableProperties()))
				$this->$varname = $value;
		}
		
		public function get($varname) {
			if(in_array($varname, $this->getAvailableProperties()))
				return $this->$varname;
		}
		
		public function getAvailableProperties() {
			return $this->settable_properties;
		}
	}
?>