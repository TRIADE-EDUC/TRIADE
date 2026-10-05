<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_connector.class.php,v 1.2 2019/12/30 15:40:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_connector extends event {
    
    protected $source_id;
    protected $locked_warehouse = false;
    
	public function get_source_id() {
	    return $this->source_id;
	}
	
	public function set_source_id($source_id) {
	    $this->source_id = $source_id;
		return $this;
	}
	
	public function set_locked_warehouse($locked) {
	    $this->locked_warehouse = boolval($locked);
	    return $this;
	}
	
	public function is_locked_warehouse() {
	    return $this->locked_warehouse;
	}
}