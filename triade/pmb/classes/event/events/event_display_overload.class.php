<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_display_overload.class.php,v 1.2.16.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/event/event.class.php');

class event_display_overload extends event {
	protected $entity_id;
	protected $overloads;
	protected $array_action_overloads;
	protected $action_overloads;
	protected $overload_type;

	public function get_entity_id() {
	    return $this->entity_id;
	}

	public function set_entity_id($entity_id) {
	    $this->entity_id = $entity_id;
		return $this;
	}

	public function add_overload($overload){
	    if(!isset($this->overloads)){
	        $this->overloads = array();
	    }
	    $this->overloads[] = $overload;
	}

	public function add_action_overload($action_overload){
	    if(!isset($this->action_overloads)){
	        $this->action_overloads = array();
	    }
	    $this->action_overloads[] = $action_overload;
	}

	public function add_array_action_overload($array_action_overload){
	    if(!isset($this->array_action_overloads)){
	        $this->array_action_overloads = array();
	    }
	    $this->array_action_overloads[] = $array_action_overload;
	}

	public function get_overloads(){
	    return $this->overloads;
	}

	public function set_overloads($overloads){
	    $this->overloads = $overloads;
	}

	public function get_action_overloads(){
	    return $this->action_overloads;
	}

	public function set_action_overloads($action_overloads){
	    $this->action_overloads = $action_overloads;
	}

	public function get_array_action_overloads(){
	    return $this->array_action_overloads;
	}

	public function set_array_action_overloads($array_action_overloads){
	    $this->array_action_overloads = $array_action_overloads;
	}

	public function set_overload_type($overload_type){
	    $this->overload_type = $overload_type;
	}

	public function get_overload_type(){
	    return $this->overload_type;
	}
}