<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_record.class.php,v 1.1 2017-01-17 15:39:13 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/event/event.class.php');

class event_record extends event {
	protected $record_id;
	
	protected $result;

	public function get_record_id() {
		return $this->record_id;
	}
	
	public function set_record_id($record_id) {
		$this->record_id = $record_id;
		return $this;
	}
	
	public function get_result() {
		return $this->result;
	}
	
	public function set_result($result) {
		$this->result = $result;
		return $this;
	}
}