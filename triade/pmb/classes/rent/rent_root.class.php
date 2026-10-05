<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_root.class.php,v 1.14 2021/04/08 07:57:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");

class rent_root {
	
	/**
	 * Type d'objet
	 * @var string
	 */
	protected $objects_type;
	
	/**
	 * Liste des objets
	 * @var rent_request
	 */
	protected $objects;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		$this->objects_type = str_replace('rent_', '', get_class($this));
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->objects = array();
	}
	
	/**
	 * Sélecteur des exercices comptables en cours
	 */
	static public function gen_selector_exercices($id_entity, $filter_type = '', $selected = 0) {
		global $msg;
	
		$display = '';
		entites::setSessionBibliId($id_entity);
		$query = exercices::listByEntite($id_entity,1);
		$display=gen_liste($query,'id_exercice','libelle', $filter_type.'_exercice', '', $selected, 0,$msg['acquisition_account_exercices_empty'],0,'');
			
		return $display;
	}
	
	public function get_objects_type() {
		return $this->objects_type;
	}
	
	public function get_objects() {
		return $this->objects;
	}
	
	public function set_objects_type($objects_type) {
		$this->objects_type = $objects_type;
	}
	
	public function set_objects($objects) {
		$this->objects = $objects;
	}
}