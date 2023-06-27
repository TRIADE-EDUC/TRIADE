<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_records.class.php,v 1.2 2015-03-12 10:18:39 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($include_path."/notice_affichage.inc.php");

class vedette_records extends vedette_element{
	
	/**
	 * Clé de l'autorité dans la table liens_opac
	 * @var string
	 */
	protected $key_lien_opac = "lien_rech_notice";

	public function set_vedette_element_from_database(){
		$this->isbd = aff_notice($this->id, 0, 1, 0, AFF_ETA_NOTICES_REDUIT);
	}
}
