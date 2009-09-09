<?php
	class SettingsStore extends Settable
	{
		protected $settable_properties = array();
		
		public function __set($varname, $value) {
			return $this->set($varname, $value);
		}
		
		public function __get($varname) {
			return $this->get($varname);
		}
		
		public function setProperties($properties) {
			if(!is_array($properties))
				$properties = func_get_args();
			elseif(is_assoc($properties)) {
				foreach($properties as $key => $value)
					$this->settable_properties[$key] = $value;
				return;
			}
			foreach($properties as $property) {
				$this->settable_properties[$property] = "";
			}
		}
		
		public function getAvailableProperties() {
			return array_keys($this->settable_properties);
		}
	}
?>