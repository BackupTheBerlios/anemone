<?php
	interface IEventArguments
	{
	}

	class EventGenericArguments implements IEventArguments
	{
	}
	
	class EventPostArguments implements IEventArguments
	{
	}
	
	class EventGetArguments implements IEventArguments
	{
	}
	
	class EventComponentIncludedArguments implements IEventArguments
	{
		public $filename;
		public $classname;
		public $class_instance;
		
		/**
		 * @param $subject SlimSystem
		 * @param $filename string
		 * @param $classname string
		 * @param $class_instance object
		 */
		function __construct(SlimSystem & $subject, $filename, $classname, & $class_instance) {
			$this->filename = $filename;
			$this->classname = $classname;
			$this->class_instance = & $class_instance;
		}
	}
	
	class EventRenderArguments implements IEventArguments
	{
		public $renderclass;
		
		function __construct(SlimSystem & $subject, IContent & $renderclass) {
			$this->renderclass = & $renderclass;
		}
	}
?>