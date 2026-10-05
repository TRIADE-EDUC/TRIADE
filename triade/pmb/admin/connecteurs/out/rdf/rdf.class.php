<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf.class.php,v 1.3 2019/07/05 13:25:15 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
global $include_path;
global $base_path;
require_once($class_path."/connecteurs_out.class.php");
require_once($class_path."/external_services.class.php");
require_once($class_path."/external_services_esusers.class.php");
require_once($class_path."/synchro_rdf.class.php");

class rdf extends connecteur_out {
	
	public function get_config_form() {
		$result = $this->msg["rdf_no_configuration_required"];
		return $result;
	}
	
	public function update_config_from_form() {
		return;
	}
	
	public function instantiate_source_class($source_id) {
		return new rdf_source($this, $source_id, $this->msg);
	}
	
	//On chargera nous même les messages si on en a besoin
	public function need_global_messages() {
		return false;
	}
	
	public function process($source_id, $pmb_user_id) {
		global $class_path;
		global $include_path;
		
		$synchro_rdf=new synchro_rdf(0,true);
	
		//Rien
		return;
	}
}

class rdf_source extends connecteur_out_source {

	public function get_config_form() {
		global $charset;
		$result = parent::get_config_form();
	
		//Adresse d'utilisation
		global $database;
		$result .= '<div class=row><label class="etiquette" for="api_exported_functions">'.$this->msg["rdf_service_endpoint"].'</label><br />';
		if ($this->id) {
			$result .= '<a target="_blank" href="ws/connector_out.php?source_id='.$this->id.'">ws/connector_out.php?source_id='.$this->id.'</a>';
		}
		else {
			$result .= $this->msg["rdf_service_endpoint_unrecorded"];
		}
		$result .= "</div>";

	
		return $result;
	}
	
}