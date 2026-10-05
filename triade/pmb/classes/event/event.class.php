<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event.class.php,v 1.4 2016/07/26 13:40:23 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

interface event_interface {
	public function set_type($type);
	public function set_sub_type($sub_type);
	public function get_type();
	public function get_sub_type();
		
}

class event implements event_interface {
	protected $type = "";
	protected $sub_type = "";
	protected $error_message = "";
	protected $elements = array();
	
	public function __construct($type, $sub_type){
		$this->set_type($type);
		$this->set_sub_type($sub_type);
	}
	public function get_type(){
		return $this->type;
	}
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	public function get_sub_type() {
		return $this->sub_type;
	}
	public function set_sub_type($sub_type) {
		$this->sub_type = $sub_type;
		return $this;
	}	
	
	public function get_error_message(){
		return $this->error_message;
	}
	
	public function set_error_message($message){
		$this->error_message = $message;
		return $this;
	}

	public function get_elements() {
		return $this->elements;
	}
	
	public function set_elements($elements) {
		$this->elements = $elements;
	}
}