<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_empr_caddie_ui.class.php,v 1.4 2018-08-06 12:20:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/caddie/list_caddie_root_ui.class.php");

class list_empr_caddie_ui extends list_caddie_root_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_caddie_content() {
		$query = "SELECT empr_caddie_content.object_id FROM empr_caddie_content ";
		$query .= $this->_get_query_filters_caddie_content();
		$query .= " AND empr_caddie_id='".static::$id_caddie."'";
		return $query;
	}
	
	protected function _get_query_base() {
		switch (static::$object_type) {
			case 'EMPR':
// 				group_concat(libelle_groupe separator ' ; ') as 'libelle_groupe'
				$query = "select empr.id_empr as id, empr.*, empr_categ.libelle as categ_libelle,
					empr_codestat.libelle as codestat_libelle,statut_libelle,location_libelle,type_abt_libelle
					from empr 
					left join empr_groupe on id_empr = empr_id 
					left join groupe on id_groupe = groupe_id 
					left join type_abts on id_type_abt=type_abt, 
					empr_categ, empr_codestat, empr_statut, docs_location
					where id_empr IN (".$this->_get_query_caddie_content().") and empr_categ=id_categ_empr and empr_codestat=idcode and empr_statut=idstatut and empr_location=idlocation
				";
				break;
		}
		return $query;
	}
	
	protected function get_exclude_fields() {
		return array(
				'empr_categ',
				'empr_codestat',
				'empr_password',
				'empr_password_is_encrypted',
				'empr_digest',
				'type_abt',
				'empr_location',
				'empr_statut',
				'cle_validation',
				'empr_subscription_action'
		);
	}
	
	protected function get_main_fields() {
		return array_merge(
				$this->get_describe_fields('empr', 'lenders', 'empr'),
				array('categ_libelle' => $this->get_describe_field('categ_libelle', 'lenders', 'empr')),
				array('codestat_libelle' => $this->get_describe_field('codestat_libelle', 'lenders', 'empr')),
				array('statut_libelle' => $this->get_describe_field('statut_libelle', 'lenders', 'empr')),
				array('location_libelle' => $this->get_describe_field('location_libelle', 'lenders', 'empr')),
				array('type_abt_libelle' => $this->get_describe_field('type_abt_libelle', 'lenders', 'empr')),
				array('groupe_libelle' => $this->get_describe_field('groupe_libelle', 'lenders', 'empr'))
		);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->add_custom_fields_available_columns('empr', 'id_empr');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'empr_nom',
				'asc_desc' => 'asc'
		);
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/circ.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&idemprcaddie='.static::$id_caddie.'&item=0';
	}
	
	protected function get_export_action() {
		global $base_path;
		global $current_module;
	
		return $base_path."/".$current_module."/caddie/action/edit.php?idemprcaddie=".static::$id_caddie;
	}
}