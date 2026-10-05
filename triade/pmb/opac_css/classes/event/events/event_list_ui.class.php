<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_list_ui.class.php,v 1.1 2023/07/06 12:30:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path.'/event/event.class.php';

class event_list_ui extends event {
	protected $url_base = "";
	protected $available_columns = [];
	protected $selection_actions = [];
	protected $object_id = [];
	protected $object;
	protected $property;
	protected $cell_content;
	
	public function get_url_base() {
		return $this->url_base;
	}
	
	public function set_url_base($url_base) {
		$this->url_base = $url_base;
		return $this->url_base;
	}
	
	public function get_object_id() {
		return $this->object_id;
	}
	
	public function set_object_id($object_id) {
		$this->object_id = $object_id;
		return $this->object_id;
	}
	
	public function get_available_columns() {
		return $this->available_columns;
	}
	
	public function set_available_columns($available_columns) {
		$this->available_columns = $available_columns;
		return $this->available_columns;
	}
	
	public function get_cell_content() {
		return $this->cell_content;
	}
	
	public function set_cell_content($cell_content) {
		$this->cell_content = $cell_content;
		return $this->cell_content;
	}
	
	public function get_selection_actions() {
		return $this->selection_actions;
	}
	
	public function set_selection_actions($selection_actions) {
		$this->selection_actions = $selection_actions;
		return $this->selection_actions;
	}
	
	public function get_object() {
		return $this->object;
	}
	
	public function set_object($object) {
		$this->object = $object;
		return $this->object;
	}
	
	public function get_property() {
		return $this->property;
	}
	
	public function set_property($property) {
		$this->property = $property;
		return $this->property;
	}
}