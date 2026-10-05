<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_indexint.class.php,v 1.2 2021/01/21 09:42:08 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/indexint.class.php");

class vedette_indexint extends vedette_element{
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_indexint";
	protected $type = TYPE_INDEXINT;
	
	public function set_vedette_element_from_database(){
		$indexint = new indexint($this->id);
		$this->isbd = "";
		if ($indexint->name_pclass) {
			$this->isbd .= "[".$indexint->name_pclass."] ";
		}
		
		$this->isbd .= $indexint->name;
		
		if ($indexint->comment) {
			$this->isbd .= " - ".$indexint->comment;
		}
		if (empty($this->isbd)){
		    $this->isbd = onto_contribution_datatype_resource_selector::get_properties_from_uri($this->id)['isbd'];
		}
	}
}
