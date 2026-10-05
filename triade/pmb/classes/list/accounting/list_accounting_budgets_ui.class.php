<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_budgets_ui.class.php,v 1.5 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_accounting_budgets_ui extends list_accounting_ui {
	
	protected function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg['acquisition_voir_bud'], ENT_QUOTES, $charset);
	}
	
	protected function _get_query_base() {
		return 'SELECT id_budget as id, budgets.* FROM budgets';
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'entity' => 'acquisition_coord_lib',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('statut');
		$this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('libelle', 'text', array('italic' => true));
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'libelle' => '103',
						'statut' => 'acquisition_statut',
						'exercice' => 'acquisition_budg_exer',
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('libelle');
		$this->add_column('statut');
		$this->add_column('exercice');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('entite', 'num_entite', 'integer');
	}
	
	protected function _get_query_order() {
		$this->applied_sort_type = 'OBJECTS';
		return '';
	}
	
	protected function _get_object_property_statut($object) {
		global $msg;
		
		switch ($object->statut) {
			case STA_BUD_VAL :
				return $msg['acquisition_statut_actif'];
			case  STA_BUD_CLO :
				return $msg['acquisition_statut_clot'];
			default:
				return $msg['acquisition_budg_pre'];
		}
	}
	
	protected function _get_object_property_exercice($object) {
		$exer = new exercices($object->num_exercice);
		return $exer->libelle;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=show&id_bibli=".$object->num_entite."&id_".$this->get_initial_name()."=".$object->id_budget."\"";
		return $attributes;
	}
	
	public function get_type_acte() {
		return 0;
	}
	
	public function get_initial_name() {
		return 'bud';
	}
}