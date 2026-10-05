<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_out_auth_ui.class.php,v 1.6 2023/04/07 09:40:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services_esusers.class.php");

class list_configuration_connecteurs_out_auth_ui extends list_configuration_connecteurs_ui {
	
	protected function _get_query_base() {
		return 'SELECT esgroup_id FROM es_esgroups';
	}
	
	protected function fetch_data() {
		global $msg;
		
		parent::fetch_data();
		//Ajoutons l'utilisateur anonyme
		$sql = "SELECT COUNT(1) FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_esgroupnum = -1";
		$anonymous_count = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		$this->add_object((object) array(
				'esgroup_id' => 0,
				'esgroup_name' => "<".$msg["admin_connecteurs_outauth_anonymgroupname"].">",
				'esgroup_fullname' => $msg["admin_connecteurs_outauth_anonymgroupfullname"],
				'connecteurs' => $anonymous_count)
		);
	}
	
	protected function get_object_instance($row) {
		if($row->esgroup_id) {
			return new es_esgroup($row->esgroup_id);
		} else {
			return parent::get_object_instance($row);
		}
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('esgroup_name');
	}
	
	protected function _add_query_filters() {
		$this->query_filters [] = 'esgroup_id <> -1';
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'esgroup_name' => 'es_group_name',
				'esgroup_fullname' => 'es_group_fullname',
				'connecteurs' => 'connector_out_authorization_authorizedsourcecount'
		);
	}
	
	protected function _get_object_property_connecteurs($object) {
		if($object->esgroup_id) {
			//Récupérons le nombre de sources autorisées dans le groupe
			$count_query = "SELECT COUNT(1) FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_esgroupnum = ".$object->esgroup_id;
			return pmb_mysql_result(pmb_mysql_query($count_query), 0, 0);
		} else {
			return $object->connecteurs;
		}
	}
	
	protected function get_edition_link($object) {
		if($object->esgroup_id) {
			return static::get_controller_url_base().'&action=edit&id='.$object->esgroup_id;
		} else {
			return static::get_controller_url_base().'&action=editanonymous';
		}
	}
	
	protected function get_button_add() {
		return "";
	}
}