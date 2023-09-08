<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_systems.class.php,v 1.2 2016-02-18 10:42:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/rent/rent_pricing_systems.tpl.php");
require_once($class_path."/rent/rent_pricing_system.class.php");
require_once($class_path."/rent/rent_pricing_system_grid.class.php");
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
	
	/**
	 * Message d'information pour l'utilisateur
	 * @var string
	 */
	protected $messages;
	
	public function __construct($id_entity=0) {
		$this->entity = new entites($id_entity*1);
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
	
	/**
	 * Liste des systèmes de tarifications
	 */
	public function get_list() {
		global $rent_pricing_systems_list_tpl;
		global $rent_pricing_system_line_tpl;
		
		$display = $rent_pricing_systems_list_tpl;
		
		$lines = '';
		$parity = 1;
		foreach ($this->pricing_systems as $pricing_system) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			
			$line = $rent_pricing_system_line_tpl;
			$line = str_replace('!!odd_even!!', $pair_impair, $line);
			$line = str_replace('!!onmousedown!!', "onclick=\"document.location='./admin.php?categ=acquisition&sub=pricing_systems&id_entity=".$this->entity->id_entite."&action=edit&id=".$pricing_system->get_id()."'\"", $line);
			$line = str_replace('!!id!!', $pricing_system->get_id(), $line);
			$line = str_replace('!!label!!', $pricing_system->get_label(), $line);
			$line = str_replace('!!associated_exercice!!', $pricing_system->get_exercice()->libelle, $line);
			$rent_pricing_system_grid = new rent_pricing_system_grid($pricing_system->get_id());
			$line = str_replace('!!grid!!', $rent_pricing_system_grid->get_display(), $line);
			$lines .= $line;
		}
		$display = str_replace('!!pricing_systems_lines!!', $lines, $display);
		$display = str_replace('!!id_entity!!', $this->entity->id_entite, $display);
		$display = str_replace('!!messages!!', $this->get_messages(), $display);
		
		return $display;
	}
	
	public function get_pricing_systems() {
		return $this->pricing_systems;
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function set_pricing_systems($pricing_systems) {
		$this->pricing_systems = $pricing_systems;
	}

	public function set_messages($messages) {
		$this->messages = $messages;
	}
}