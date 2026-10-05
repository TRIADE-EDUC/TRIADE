<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_empr_caddie_content_ui.class.php,v 1.2 2020/11/05 12:26:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_empr_caddie_content_ui extends list_caddie_content_root_ui {
	
	protected function _get_query_caddie_content() {
		$query = "SELECT empr_caddie_content.object_id FROM empr_caddie_content ";
		$query .= $this->_get_query_filters_caddie_content();
		$query .= " AND empr_caddie_id='".static::$id_caddie."'";
		return $query;
	}
	
	protected function _get_query_base() {
		switch (static::$object_type) {
			case 'EMPR':
				$query = "SELECT DISTINCT empr.id_empr as id, empr.*, empr_categ.libelle AS categ_libelle, empr_codestat.libelle AS codestat_libelle, 
				          type_abt_libelle, statut_libelle, location_libelle, GROUP_CONCAT(libelle_groupe SEPARATOR ' ; ') as groupe_libelle
				          FROM empr 
                          LEFT JOIN empr_groupe ON id_empr = empr_id 
				          LEFT JOIN groupe ON id_groupe = groupe_id 
				          LEFT JOIN type_abts ON id_type_abt = type_abt, empr_categ, empr_codestat, empr_statut, docs_location 
				          WHERE id_empr IN (".$this->_get_query_caddie_content().") 
			              AND empr_categ = id_categ_empr 
			              AND empr_codestat = idcode 
			              AND empr_statut = idstatut 
			              AND empr_location = idlocation";
				break;
			default:
			    $query = "";
			    break;
		}
		return $query;
	}
	
	protected function _get_query_order() {
	    return ' GROUP BY empr.id_empr '.parent::_get_query_order();
	}
	
	protected function get_exclude_fields() {
		return array(
			'empr_categ',
			'empr_codestat',
			'empr_password',
			'empr_password_is_encrypted',
			'empr_pnb_password',
			'empr_pnb_password_hint',
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
	    $this->add_applied_sort('empr_nom');
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