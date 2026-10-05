<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_users_group.class.php,v 1.2.14.1 2024/09/06 14:44:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_users_group extends event {	
	
	protected $group_id;	
	protected $group_name = "";	
	protected $id_caddie;
	protected $content_form = '';
	
	public function get_group_id() {
		return $this->group_id;
	}
	
	public function set_group_id($group_id) {
		$this->group_id = $group_id;
		return $this;
	}
	
	public function get_group_name() {
		return $this->group_name;
	}
	
	public function set_group_name($group_name) {
		$this->group_name = $group_name;
		return $this;
	}
	
	public function get_id_caddie() {
		return $this->id_caddie;
	}
	
	public function set_id_caddie($id_caddie) {
		$this->id_caddie = $id_caddie;
		return $this;
	}
	
	public function get_content_form() {
	    return $this->content_form;
	}
	
	public function set_content_form($content_form) {
	    $this->content_form = $content_form;
	}
}
