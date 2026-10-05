<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesAccessRights.class.php,v 1.3 2023/03/16 10:49:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/external_services.class.php");
require_once("$include_path/mysql_connect.inc.php");

/*
 ATTENTION: Si vous modifiez de fichier vous devez probablement modifier le fichier pmbesIndex.class.php
*/
class pmbesAccessRights extends external_services_api_class {
	
	protected function initialization_rights($domain, $keep_specific_rights=1, $delete_calculated_rights=0) {
		$ac= new acces();
		$dom= $ac->setDomain($domain);
		$nb_done=0;
		$nb=$dom->getNbResourcesToUpdate();
		$deleted_calculated_rights=0;
		if($nb) {
			if($delete_calculated_rights) {
				$dom->cleanResources();
				$deleted_calculated_rights=1;
			}
			while($nb_done < $nb) {
				$nb_done=$dom->applyDomainRights($nb_done,$keep_specific_rights);
			}
		}
		return array(
				'nb_done' => $nb_done,
				'nb_total' => $nb,
				'deleted_calculated_rights' => $deleted_calculated_rights
		);
	}
	
	protected function initialization_admin_rights($domain, $keep_specific_rights=1, $delete_calculated_rights=0) {
		global $msg, $PMBusername;
		
		$informations=array();
		if (SESSrights & ADMINISTRATION_AUTH) {
			$informations = $this->initialization_rights($domain, $keep_specific_rights, $delete_calculated_rights);
			$initialized = true;
		} else {
			$informations['error_message'] = sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
			$initialized = false;
		}
		return array(
				'initialized' => $initialized,
				'informations' => $informations
		);
	}
	
	//droits d'acces utilisateur/notice
	public function user_notice($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(1, $keep_specific_rights, $delete_calculated_rights);
	}
	
	//droits d'acces emprunteur/notice
	public function empr_notice($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(2, $keep_specific_rights, $delete_calculated_rights);
	}
	
	//droits d'acces emprunteur/docnums
	public function empr_docnum($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(3, $keep_specific_rights, $delete_calculated_rights);
	}
	
	//droits d'acces emprunteur/espaces de contribution
	public function empr_contribution_area($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(4, $keep_specific_rights, $delete_calculated_rights);
	}
	
	//droits d'acces emprunteur/scenarios de contribution
	public function empr_contribution_scenario($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(5, $keep_specific_rights, $delete_calculated_rights);
	}
	
	//droits d'acces modérateurs/contributeurs
	public function contribution_moderator_empr($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(6, $keep_specific_rights, $delete_calculated_rights);
	}
	
	//droits d'acces emprunteur/rubrique
	public function empr_cms_section($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(7, $keep_specific_rights, $delete_calculated_rights);
	}
	
	//droits d'acces emprunteur/article
	public function empr_cms_article($keep_specific_rights=1, $delete_calculated_rights=0) {
		return $this->initialization_admin_rights(8, $keep_specific_rights, $delete_calculated_rights);
	}
	
}

?>