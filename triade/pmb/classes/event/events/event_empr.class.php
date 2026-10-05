<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_empr.class.php,v 1.1 2016/07/26 13:38:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path.'/event/event.class.php');

class event_empr extends event {
	
	protected $id_empr;
	
	protected $template_content = '';
	
	public function get_id_empr() {
		return $this->id_empr;
	}
	
	public function set_id_empr($id_empr) {
		$this->id_empr = $id_empr;
		return $this;
	}
	
	public function get_template_content() {
		return $this->template_content;
	}
	
	public function set_template_content($template_content) {
		$this->template_content .= $template_content;
	}
}
