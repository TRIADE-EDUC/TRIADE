<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_cashdesk_ui.class.php,v 1.1.2.3 2024/09/11 14:19:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_cashdesk_ui extends list_ui {
		
	protected $cashdesks;
	
	protected function _get_query_base() {
	    
	    $query = "SELECT * FROM cashdesk";
	    return $query;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'cashdesks' => 'cashdesk_edition_filter',
						'date_effective' => '653',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'cashdesks' => array(),
                'transactypes' => array(),
				'date_effective_start' => '',
				'date_effective_end' => '',
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('cashdesk_name');
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_column('cashdesk_name', 'align', 'left');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('cashdesks');
		$this->set_filter_from_form('date_effective_start');
		$this->set_filter_from_form('date_effective_end');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'cashdesk':
				$query = 'select cashdesk_id as id, cashdesk_name as label from cashdesk order by label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_cashdesks() {
		return $this->get_search_filter_multiple_selection($this->get_selection_query('cashdesk'), 'cashdesks', "--");
	}
	
	protected function get_search_filter_date_effective() {
		return $this->get_search_filter_interval_date('date_effective');
	}
	
	protected function _add_query_filters() {
	    $this->_add_query_filter_multiple_restriction('cashdesks', 'cashdesk_id', 'integer');
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'cashdesk':
				return "select cashdesk_name from cashdesk where cashdesk_id = ".$this->filters[$property];
			case 'cashdesks':
				return "select cashdesk_name from cashdesk where cashdesk_id IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}
	
	protected function _get_query_human_date_effective() {
		return $this->_get_query_human_interval_date('date_effective');
	}
	
	protected function _get_query_human() {
		$humans = $this->_get_query_human_main_fields();
		return $this->get_display_query_human($humans);
	}
	
	public function format_price($price) {
	    global $pmb_fine_precision;
	    
	    if (!$pmb_fine_precision) $pmb_fine_precision=2;
	    return 	number_format(floatval($price), $pmb_fine_precision, '.', ' ');
	}
	
	protected function get_cashdesk($id) {
		if(!isset($this->cashdesks[$id])) {
			$this->cashdesks[$id] = new cashdesk($id);
		}
		return $this->cashdesks[$id];
	}
}