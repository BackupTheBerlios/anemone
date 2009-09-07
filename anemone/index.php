<?php
	header("Content-Type: text/plain");
	error_reporting(E_ALL);
	
	ini_set("include_path", ini_get("include_path").PATH_SEPARATOR."./inc/".PATH_SEPARATOR."./lib/");
	function __autoload($class_name) {
		if(class_exists($class_name, false))
			return;
		$file_to_include = $class_name;
		if(substr($file_to_include, 0, 5) == "Event")
			$file_to_include = "EventTypes";
	    require_once $file_to_include . '.php';
	}
	
	$system = SlimSystem::getInstance();
	$system->initDefaultComponents();
	echo $system->render("index.tal");
?>