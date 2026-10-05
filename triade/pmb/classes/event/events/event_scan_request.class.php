<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_scan_request.class.php,v 1.1 2016/05/10 10:07:59 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';
require_once($class_path.'/authority.class.php');

class event_scan_request extends event {
	protected $concept_uri = '';
	protected $template_content = '';
	
	public function get_concept_uri() {
		return $this->concept_uri;
	}
	
	public function set_concept_uri($concept_uri) {
		$this->concept_uri = $concept_uri;
	}
	
	public function get_template_content() {
		return $this->template_content;
	}
	
	public function set_template_content($template_content) {
		$this->template_content = $template_content;
	}
}