<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_caddie.class.php,v 1.1 2016/04/14 14:50:32 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_caddie extends event {
	protected $id_caddie = 0;
	protected $name = '';
	protected $nb_items = 0;

	public function get_id_caddie() {
		return $this->id_caddie;
	}

	public function set_id_caddie($id_caddie) {
		$this->id_caddie = $id_caddie;
	}

	public function get_name() {
		return $this->name;
	}

	public function set_name($name) {
		$this->name = $name;
	}

	public function get_nb_items() {
		return $this->nb_items;
	}

	public function set_nb_items($nb_items) {
		$this->nb_items = $nb_items;
	}
}
