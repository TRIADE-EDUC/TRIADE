<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: agnostic.class.php,v 1.2 2016-11-04 11:34:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");

class agnostic extends connector {
    
    public function get_id() {
    	return "agnostic";
    }
    
    //Est-ce un entrepot ?
	public function is_repository() {
		return 1;
	}
	
	//Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	public function fetch_default_global_values() {
		parent::fetch_default_global_values();
		$this->repository=1;
	}
		
	public function cancel_maj($source_id) {
		return true;
	}
	
	public function break_maj($source_id) {
		return true;
	}
}