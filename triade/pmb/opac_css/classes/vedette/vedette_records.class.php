<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_records.class.php,v 1.4 2021/01/21 09:42:07 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($class_path."/vedette/vedette_element.class.php");
require_once($include_path."/notice_affichage.inc.php");

class vedette_records extends vedette_element{
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_notice";
	protected $type = 'record';

	public function set_vedette_element_from_database(){
        $this->isbd = notice::get_notice_title($this->id);
        if (empty($this->isbd)){
            $this->isbd = onto_contribution_datatype_resource_selector::get_properties_from_uri($this->id)['isbd'];
        }
	}
}
