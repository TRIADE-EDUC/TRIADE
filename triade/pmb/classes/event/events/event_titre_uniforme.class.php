<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_titre_uniforme.class.php,v 1.3.14.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';
require_once($class_path.'/authority.class.php');

class event_titre_uniforme extends event {

	protected $titre_uniforme_id;

	protected $source_type;

	protected $source_id;

	protected $titre_uniforme_isbd;

	protected $replacement_id;

	public function get_titre_uniforme_id() {
		return $this->titre_uniforme_id;
	}

	public function set_titre_uniforme_id($titre_uniforme_id) {
	    $this->titre_uniforme_id = (int) $titre_uniforme_id;
		return $this;
	}

	public function get_class_titre_uniforme (){
	    $authority = new authority(0, $this->get_titre_uniforme_id(), AUT_TABLE_TITRES_UNIFORMES);
	    return $authority->get_object_instance();
	}

	public function get_source_type() {
		return $this->source_type;
	}

	public function set_source_type($source_type) {
		$this->source_type = $source_type;
	}

	public function get_source_id() {
		return $this->source_id;
	}

	public function set_source_id($source_id) {
	    $this->source_id = (int) $source_id;
	}

	public function get_titre_uniforme_isbd() {
		return $this->titre_uniforme_isbd;
	}

	public function set_titre_uniforme_isbd($titre_uniforme_isbd) {
		$this->titre_uniforme_isbd = $titre_uniforme_isbd;
	}

	public function get_replacement_id() {
	    return $this->replacement_id;
	}

	public function set_replacement_id($replacement_id) {
	    $this->replacement_id = $replacement_id;
	}

}