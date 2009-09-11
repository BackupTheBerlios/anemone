<?php
	class Document extends Settable implements IContent, IComponent, IObserver
	{
		private $parent_content;
		public $content;
		public $title;
		protected $settable_properties = array("title");
		
		public static function createInstance(SlimSystem & $system) {
			$c = __CLASS__;
			return new $c();
		}
		
		public function setSubject(Observable & $subject){
			$subject->register($this, Observable::EVENT_RENDER_START);
		}
		
		public function notify(Observable & $subject, $eventType, IEventArguments $arguments) {
			if($subject instanceof SlimSystem && $subject->get("root_class") != __CLASS__)
				return;
			$arguments->renderclass->setParent($this);
		}
		
		public function getCssFiles() {
			return array();
		}
		
		public function getJsFiles() {
			return array();
		}
		
		public function getMetadata() {
			return array();
		}
		
		public function getPages() {}
		
		public function render() {
			$tpl = "index.tal";
			$tpl_engine = SlimSystem::getInstance()->getTplEngine("tal");
			return $tpl_engine->parse("./tpl/", $tpl, $this);
		}
		
		public function setParent(IContent & $parent_content) {
			$this->parent_content = $parent_content;
		}
		
		public function getParent() {
			return $this->parent_content;
		}
	}
?>