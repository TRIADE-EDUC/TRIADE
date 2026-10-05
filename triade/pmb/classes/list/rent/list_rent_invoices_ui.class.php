<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_invoices_ui.class.php,v 1.14.2.1 2024/07/19 07:01:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_rent_invoices_ui extends list_rent_ui {
		
	protected $marclist_rent_destination;
	
	protected function _get_query_base() {
		$query = "SELECT distinct id_invoice FROM rent_invoices 
			JOIN rent_accounts_invoices ON account_invoice_num_invoice = id_invoice
			JOIN rent_accounts ON id_account = account_invoice_num_account";
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new rent_invoice($row->id_invoice);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'entity' => 'acquisition_coord_lib',
						'exercice' => 'acquisition_budg_exer',
				        'exercices' => 'acquisition_menu_ref_compta',
						'type' => 'acquisition_account_type_name',
						'num_publisher' => 'acquisition_account_num_publisher',
						'num_supplier' => 'acquisition_account_num_supplier',
						'status' => 'acquisition_invoice_status',
						'date' => 'acquisition_invoice_date',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$id_entity = entites::getSessionBibliId();
		$query = exercices::listByEntite($id_entity);
		$result = pmb_mysql_query($query);
		$id_exercice = 0;
		if($result && pmb_mysql_num_rows($result)) {
			$id_exercice = pmb_mysql_result($result, 0, 'id_exercice');
		}
		$filter_exercices = array_key_exists('exercices', $this->selected_filters);
		$this->filters = array(
				'entity' => $id_entity,
                'exercice' => ($filter_exercices ? 0 : $id_exercice),
                'exercices' => ($filter_exercices ? [$id_exercice] : []),
				'type' => '',
				'num_publisher' => '',
				'num_supplier' => '',
				'num_pricing_system' => '',
				'status' => 0,
				'date_start' => '',
				'date_end' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
		$this->add_selected_filter('exercice');
		$this->add_selected_filter('type');
		$this->add_selected_filter('num_publisher');
		$this->add_selected_filter('num_supplier');
		$this->add_selected_filter('status');
		$this->add_selected_filter('date');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id' => 'acquisition_invoice_id',
						'num_user' => 'acquisition_invoice_num_user',
						'date' => 'acquisition_invoice_date',
						'num_publisher' => 'acquisition_invoice_num_publisher',
						'num_supplier' => 'acquisition_invoice_num_supplier',
						'status' => 'acquisition_invoice_status',
						'valid_date' => 'acquisition_invoice_valid_date',
						'destination_name' => 'acquisition_invoice_destination_name',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('id');
		$this->add_column('num_user');
		$this->add_column('date');
		$this->add_column('num_publisher');
		$this->add_column('num_supplier');
		$this->add_column('status');
		$this->add_column('valid_date');
		$this->add_column('destination_name');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_invoice';
	        case 'date':
	        case 'valid_date' :
	            return 'invoice_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('status', 'align', 'center');
		$this->set_setting_column('valid_date', 'align', 'center');
		$this->set_setting_column('id', 'datatype', 'integer');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('status', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_status() {
		global $msg;
		
		$options = array(
				0 => $msg['acquisition_account_type_select_all'],
				1 => $msg['acquisition_invoice_status_new'],
				2 => $msg['acquisition_invoice_status_validated'],
		);
		return $this->get_search_filter_simple_selection('', 'status', '', $options);
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('exercice', 'account_num_exercice', 'integer');
		$this->_add_query_filter_multiple_restriction('exercices', 'account_num_exercice', 'integer');
		$this->_add_query_filter_simple_restriction('type', 'account_type');
		$this->_add_query_filter_simple_restriction('num_publisher', 'account_num_publisher', 'integer');
		$this->_add_query_filter_simple_restriction('num_supplier', 'account_num_supplier', 'integer');
		$this->_add_query_filter_simple_restriction('num_pricing_system', 'account_num_pricing_system', 'integer');
		$this->_add_query_filter_simple_restriction('status', 'invoice_status', 'integer');
		$this->_add_query_filter_interval_restriction('date', 'invoice_date', 'datetime');
	}
	
	protected function _get_object_property_num_publisher($object) {
		$accounts = $object->get_accounts();
		if(count($accounts)) {
			if(isset($accounts[0]->get_publisher()->display)) {
				return $accounts[0]->get_publisher()->display;
			}
		}
		return '';
	}
	
	protected function _get_object_property_num_supplier($object) {
		$accounts = $object->get_accounts();
		if(count($accounts)) {
			if(isset($accounts[0]->get_supplier()->raison_sociale)) {
				return $accounts[0]->get_supplier()->raison_sociale;
			}
		}
		return '';
	}
	
	protected function _get_object_property_num_user($object) {
		return $object->get_user()->prenom.' '.$object->get_user()->nom;
	}
	
	protected function _get_object_property_status($object) {
		return $object->get_status_label();
	}
	
	protected function _get_object_property_destination_name($object) {
		if(!isset($this->marclist_rent_destination)) {
			$this->marclist_rent_destination = new marc_list('rent_destination');
		}
		return $this->marclist_rent_destination->table[$object->get_destination()];
	}

	protected function _get_query_human_status() {
		global $msg;
		if($this->filters['status'] == 1) {
			return $msg['acquisition_invoice_status_new'];
		} elseif($this->filters['status'] == 2){
			return $msg['acquisition_invoice_status_validated'];
		}
		return '';
	}
	
	protected function _get_query_human() {
		$humans = $this->_get_query_human_main_fields();
		return $this->get_display_query_human($humans);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		global $id_bibli;
		
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id_bibli=".$id_bibli."&id=".$object->get_id()."\"";
		return $attributes;
	}
	
	protected function init_default_selection_actions() {
		global $msg, $base_path;
		
		parent::init_default_selection_actions();
		$gen_invoices_link = array(
				'openPopUp' => $base_path."/pdf.php?pdfdoc=account_invoice",
				'openPopUpTitle' => 'lettre'
		);
		$this->add_selection_action('gen_invoices', $msg['acquisition_invoice_generate'], '', $gen_invoices_link);
		
		$validate_invoices_link = array(
				'href' => static::get_controller_url_base()."&action=validate"
		);
		$this->add_selection_action('validate_invoices', $msg['acquisition_invoice_validate'], '', $validate_invoices_link);
	}
	
	public function has_rights() {
	    if (!(SESSrights & ACQUISITION_ACCOUNT_INVOICE_AUTH)) {
	        return false;
	    }
	    return parent::has_rights();
	}
}