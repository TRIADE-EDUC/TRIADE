<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_diff_ui.class.php,v 1.1 2023/01/05 11:11:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc_diff.class.php");
require_once($class_path."/expl.class.php");

class list_serialcirc_diff_ui extends list_serialcirc_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT * FROM serialcirc_diff
				JOIN serialcirc ON serialcirc_diff.num_serialcirc_diff_serialcirc = serialcirc.id_serialcirc';
		return $query;
	}
	
	protected function get_serialcirc($object) {
		return serialcirc_diff::get_instance($object->num_serialcirc_diff_serialcirc);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
// 		$this->add_applied_sort('serial');
// 		$this->add_applied_sort('abonnement');
		$this->add_applied_sort('order');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'serial' => 'serialcirc_circ_list_bull_circulation_perodique',
						'abonnement' => 'serialcirc_circ_list_bull_circulation_abonnement',
						'empr_type' => 'serialcirc_diff_empr_type',
						'empr_name' => 'serialcirc_diff_empr_name',
						'group_name' => 'serialcirc_diff_empr_type_group_name',
						'duration' => 'serialcirc_diff_day_number',
						'order' => 'serialcirc_diff_order',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['serialcirc_diff_title_form'], ENT_QUOTES, $charset);
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('empr_type');
		$this->add_column('empr_name');
		$this->add_column('group_name');
		$this->add_column('duration');
		$this->add_column('order');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('duration', 'datatype', 'integer');
		$this->set_setting_column('order', 'datatype', 'integer');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'abts_abts' => 'abts_onglet_abt',
						'serials' => 'serials_query',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'abts_abts' => array(),
				'serials' => array(),
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'serial', 1 => 'abonnement');
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('abts_abts');
		$this->add_selected_filter('serials');
	}
	
	protected function init_default_selection_actions() {
		parent::init_default_selection_actions();
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('abts_abts', 'integer');
		$this->set_filter_from_form('serials', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'abts_abts':
				$statuses = abts_status::get_ids_bulletinage_active();
				$query = 'SELECT abt_id as id, abt_name as label FROM abts_abts WHERE statut_id IN ('.implode(',', $statuses).') ORDER BY label';
				break;
			case 'serials':
				$query = 'SELECT distinct notice_id as id, tit1 as label FROM notices JOIN abts_abts ON num_notice = notice_id ORDER BY label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_abts_abts() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('abts_abts'), 'abts_abts', $msg["all"]);
	}
	
	protected function get_search_filter_serials() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('serials'), 'serials', $msg["all"]);
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_multiple_restriction('abts_abts', 'abts_abts.abt_id', 'integer');
		$this->_add_query_filter_multiple_restriction('serials', 'bulletin_notice', 'integer');
	}
	
	protected function _get_object_property_serial($object) {
		return $this->get_serialcirc($object)->serial_info['serial_name'];
	}
	
	protected function _get_object_property_abonnement($object) {
		return $this->get_serialcirc($object)->abt_name;
	}
	
	protected function _get_object_property_empr_type($object) {
		global $msg;
		
		switch (intval($object->serialcirc_diff_empr_type)) {
			case 1:
				return $msg['serialcirc_diff_empr_type_group'];
			default:
				return $msg['serialcirc_diff_empr_type_empr'];
		}
	}
	
	protected function _get_object_property_type_diff($object) {
		return $object->serialcirc_diff_type_diff;
	}
	
	protected function _get_object_property_empr_name($object) {
		return emprunteur::get_name($object->num_serialcirc_diff_empr);
	}
	
	protected function _get_object_property_group_name($object) {
		return $object->serialcirc_diff_group_name;
	}
	
	protected function _get_object_property_duration($object) {
		return $object->serialcirc_diff_duration;
	}
	
	protected function _get_object_property_order($object) {
		return $object->serialcirc_diff_order;
	}
	
	
	protected function _get_query_human_abts_abts() {
		if(!empty($this->filters['abts_abts'])) {
			$labels = array();
			foreach ($this->filters['abts_abts'] as $abt_id) {
				$abts_abonnement = new abts_abonnement($abt_id);
				$labels[] = $abts_abonnement->abt_name;
			}
			return implode(', ', $labels);
		}
		return '';
	}
	
	protected function _get_query_human_serials() {
		if(!empty($this->filters['serials'])) {
			$labels = array();
			foreach ($this->filters['serials'] as $serial_id) {
				$labels[] = notice::get_notice_title($serial_id);
			}
			return implode(', ', $labels);
		}
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/circ.php?categ=serialcirc&sub=list_diff';
	}
}