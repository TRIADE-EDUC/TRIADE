<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_systems.class.php,v 1.3.10.1 2025/03/19 11:04:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/rent/rent_pricing_systems.tpl.php");
require_once($class_path."/rent/rent_pricing_system.class.php");
require_once($class_path."/entites.class.php");

class rent_pricing_systems {	

	/**
	 * Instance de la classe entites
	 * @var entites
	 */
	protected $entity;
	
	/**
	 * Systèmes de tarification
	 * @var rent_pricing_system
	 */
	protected $pricing_systems;
	
	public function __construct($id_entity=0) {
		$this->entity = new entites($id_entity);
		$this->fetch_data();
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {

		$this->pricing_systems = array();
		$query = 'select * from rent_pricing_systems join exercices on rent_pricing_systems.pricing_system_num_exercice = exercices.id_exercice and num_entite='.$this->entity->id_entite;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->pricing_systems[] = new rent_pricing_system($row->id_pricing_system);
			}
		}
		$this->messages = '';
	}
	
	public function get_pricing_systems() {
		return $this->pricing_systems;
	}
	
	public function set_pricing_systems($pricing_systems) {
		$this->pricing_systems = $pricing_systems;
	}
}