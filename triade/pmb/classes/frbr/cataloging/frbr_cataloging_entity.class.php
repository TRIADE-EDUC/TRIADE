<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_entity.class.php,v 1.4 2018-02-15 09:12:49 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_cataloging_entity {
	
	protected $name;
	protected $uri;

	/**
	 * Constructeur
	 */
	public function __construct($uri, $name) {
		$this->uri = $uri;
		$this->name = $name;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function get_label() {
		$label = frbr_cataloging_entities::get_label($this->uri);
	 	return $label;
	}
	
	public function get_entity_from_node() {
		
	}
	
	public function get_links() {
		
	}
}