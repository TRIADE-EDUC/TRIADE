<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_author.class.php,v 1.5 2016/09/09 14:00:49 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';
require_once($class_path.'/authority.class.php');

class event_author extends event {
	protected $id_author;
	protected $replacement_id;
	protected $checked_elts = array();

	public function get_id_author() {
		return $this->id_author;
	}
	
	public function set_id_author($id_author) {
		$this->id_author = $id_author;
		return $this;
	}
	
	public function get_replacement_id() {
		return $this->replacement_id;
	}
	
	public function set_replacement_id($replacement_id) {
		$this->replacement_id = $replacement_id;
	}
	
	public function get_class_author (){
	    $authority = new authority(0, $this->get_id_author(), AUT_TABLE_AUTHORS);
		return $authority->get_object_instance();
	}
}