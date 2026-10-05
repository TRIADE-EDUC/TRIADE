<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_series.class.php,v 1.3 2021/01/21 09:42:08 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/serie.class.php");

class vedette_series extends vedette_element{
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_serie";
	protected $type = TYPE_SERIE;
	
	public function set_vedette_element_from_database(){
		$serie = new serie($this->id);
		$this->isbd = $serie->get_isbd();
		if (empty($this->isbd)){
		    $this->isbd = onto_contribution_datatype_resource_selector::get_properties_from_uri($this->id)['isbd'];
		}
	}
}
