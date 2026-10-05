<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_entity.class.php,v 1.1 2023/04/06 12:18:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path.'/event/event.class.php';

class event_entity extends event {	
	
	protected $entity_id;
	protected $entity_type;
	protected $user_id;
	protected $user_grp_num;
	
	public function get_entity_id() {
		return $this->entity_id;
	}
	
	public function get_entity_type() {
		return $this->entity_type;
	}
	
	public function set_entity_id($entity_id) {
		$this->entity_id = intval($entity_id);
		return $this;
	}
	
	public function set_entity_type($entity_type) {
		$this->entity_type = intval($entity_type);
		return $this;
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	public function set_user_id($user_id) {
		$this->user_id = intval($user_id);
		$this->user_grp_num = user::get_grp_num($this->user_id);
		return $this;
	}
	
	public function get_user_grp_num() {
		if(empty($this->user_grp_num)) {
			$this->user_grp_num = user::get_grp_num($this->user_id);
		}
		return $this->user_grp_num;
	}
}
