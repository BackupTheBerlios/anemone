<?php
	interface IComponent
	{
		/**
		 * @param SlimSystem $system
		 * @return IComponent
		 */
		static function createInstance(SlimSystem & $system);
	}
?>