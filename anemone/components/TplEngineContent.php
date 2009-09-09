<?php
	class TplEngineContent extends Settable implements IContent, IObserver, IComponent
	{
		public $template;
		public $tpl_dir;
		
		protected $settable_properties = array("template", "tpl_dir");
		
		private $template_engines = array();
		private $parent_content;
		private $output;
		
		public function __construct(SlimSystem & $system) {
		}
		
		public function setSubject(Observable & $subject) {
			$subject->register($this, Observable::EVENT_COMPONENT_INCLUDED);
		}
		
		public function setParentContent(IContent & $parent_content) {
			$this->parent_content = & $parent_content;
		}
		
		public function getContentType() {
			return "";
		}
		
		public function registerTemplateEngine(ITplEngine & $engine) {
			$exts = $engine->getFileExtensions();
			for($i = 0; $i < count($exts); $i++) {
				$this->template_engines[$exts[$i]] = & $engine;
			}
		}
		
		public function notify(Observable & $subject, $eventType, IEventArguments $arguments) {
			/**
			 * @var EventComponentIncludedArguments
			 */
			$c = & $arguments->class_instance;
			if($c instanceof ITplEngine) {
				$this->registerTemplateEngine($c);
			}
		}
		
		public function render() {
			$this->output = "";
			$ext = substr($this->template, strrpos($this->get("template"), ".")+1);
			$tpl_engine = $this->getTplEngine($ext);
			if($tpl_engine == null){
				// default: return plain text
				return nl2br(htmlentities(file_get_contents($this->tpl_dir.$this->template)));
			}
			$this->output = $tpl_engine->parse($this->tpl_dir, $this->template, null);
			return $this->output;
		}
		
		/**
		 * @param $ext string
		 * @return ITplEngine
		 */
		function getTplEngine($ext) {
			if(!array_key_exists($ext, $this->template_engines))
				return null;
			return $this->template_engines[$ext];
		}
		
		public function getOutput() {
			return $this->output;
		}
	}
?>