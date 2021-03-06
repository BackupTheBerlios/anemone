<?php
	header("Content-Type: text/plain");
	error_reporting(E_ALL);
	
	include dirname(__FILE__)."/util.php";
	ini_set("include_path", ini_get("include_path").PATH_SEPARATOR."./inc/".PATH_SEPARATOR."./lib/");
	function __autoload($class_name) {
		if(class_exists($class_name, false))
			return;
		$file_to_include = $class_name;
		if(substr($file_to_include, 0, 5) == "Event" && substr($file_to_include, -9, 9) == "Arguments")
			$file_to_include = "EventTypes";
	    require_once $file_to_include . '.php';
	}
	
	$system = SlimSystem::getInstance();
	$system->loadDefaultComponents();
	echo $system->render();
?>