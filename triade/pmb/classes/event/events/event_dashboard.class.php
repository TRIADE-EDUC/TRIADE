<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_dashboard.class.php,v 1.1 2016/04/14 14:50:32 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_dashboard extends event {
	protected $module;
	protected $content = '';
	
	public function get_module() {
		return $this->module;
	}
	
	public function set_module($module) {
		$this->module = $module;
	}
	
	public function get_content() {
	  return $this->content;
	}
	
	public function set_content($content) {
	  $this->content = $content;
	}
	
	public function add_content($content){
		$this->content.= $content;
	}
}
