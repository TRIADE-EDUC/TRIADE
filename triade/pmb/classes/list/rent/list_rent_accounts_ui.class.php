<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_accounts_ui.class.php,v 1.15.2.7 2024/11/04 14:40:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_rent_accounts_ui extends list_rent_ui {
		
	protected function _get_query_base() {
		$query = "SELECT id_account FROM rent_accounts";
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new rent_account($row->id_account);
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
						'request_type' => 'acquisition_account_request_type_name',
						'type' => 'acquisition_account_type_name',
				        'title' => 'acquisition_account_title',
						'num_publisher' => 'acquisition_account_num_publisher',
						'num_supplier' => 'acquisition_account_num_supplier',
						'num_author' => 'acquisition_account_num_author',
						'invoiced' => 'acquisition_account_invoiced_filter',
						'request_status' => 'acquisition_account_request_status',
						'num_pricing_system' => 'acquisition_account_num_pricing_system',
						'event_date' => 'acquisition_account_event_date',
						'date' => 'acquisition_account_date',
				    	'diffusion_date' => 'acquisition_account_diffusion_date',
						'rights_date' => 'acquisition_account_rights_date',
						'unlimited_rights' => 'acquisition_account_unlimited_rights',
				        'comment' => 'acquisition_account_comment',
				        'pricing_system_except' => 'acquisition_account_pricing_system_except',
                        'pricing_systems' => 'acquisition_account_pricing_systems',
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
				'request_type' => '',
				'type' => '',
                'title' => '',
                'num_publisher' => '',
				'num_supplier' => '',
				'num_author' => '',
				'num_pricing_system' => '',
				'web' => '',
				'date_start' => '',
				'date_end' => '',
				'event_date_start' => '',
				'event_date_end' => '',
				'diffusion_date_start' => '',
				'diffusion_date_end' => '',
			    'rights_date_start' => '',
			    'rights_date_end' => '',
		    	'unlimited_rights' => -1,
		    	'invoiced' => '',
		    	'request_status' => 0,
                'comment' => '',
                'pricing_system_except' => -1,
                'pricing_systems' => []
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
		$this->add_selected_filter('exercices');
		$this->add_selected_filter('type');
		$this->add_selected_filter('num_publisher');
		$this->add_selected_filter('num_supplier');
		$this->add_selected_filter('num_author');
		$this->add_selected_filter('invoiced');
		$this->add_selected_filter('request_status');
		$this->add_selected_filter('num_pricing_system');
		$this->add_selected_filter('event_date');
		$this->add_selected_filter('diffusion_date');
		$this->add_selected_filter('rights_date');
		$this->add_selected_filter('unlimited_rights');
		$this->add_selected_filter('title');
		$this->add_selected_filter('comment');
		$this->add_selected_filter('pricing_system_except');
	}
	
	protected function get_button_add() {
	    global $msg;
	    
	    return $this->get_button('edit', $msg['acquisition_new_account'], '&id=0');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id' => 'acquisition_account_id',
						'num_user' => 'acquisition_account_num_user',
						'request_type_name' => 'acquisition_account_request_type_name',
						'type_name' => 'acquisition_account_type_name',
						'date' => 'acquisition_account_date',
    				    'receipt_limit_date' => 'acquisition_account_receipt_limit_date',
    				    'receipt_effective_date' => 'acquisition_account_receipt_effective_date',
    				    'return_date' => 'acquisition_account_return_date',
						'title' => 'acquisition_account_title',
    				    'event_date' => 'acquisition_account_event_date',
    				    'event_formation' => 'acquisition_account_event_formation',
    				    'event_orchestra' => 'acquisition_account_event_orchestra',
    				    'event_place' => 'acquisition_account_event_place',
						'num_publisher' => 'acquisition_account_num_publisher',
						'num_supplier' => 'acquisition_account_num_supplier',
						'num_author' => 'acquisition_account_num_author',
				        'num_pricing_system' => 'acquisition_account_num_pricing_system',
    				    'time' => 'acquisition_account_time',
    				    'percent' => 'acquisition_account_percent',
    				    'price' => 'acquisition_account_price',
    				    'web_percent' => 'acquisition_account_web_percent',
    				    'web_price' => 'acquisition_account_web_price',
				        'comment' => 'acquisition_account_comment',
						'request_status' => 'acquisition_account_request_status',
				        'num_invoice' => 'acquisition_account_num_invoice',
				        'diffusion_date' => 'acquisition_account_diffusion_date',
    				    'rights_date' => 'acquisition_account_rights_date',
						'state_icon' => 'acquisition_account_state_icon',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('id');
		$this->add_column('num_user');
		$this->add_column('request_type_name');
		$this->add_column('type_name');
		$this->add_column('date');
		$this->add_column('title');
		$this->add_column('num_publisher');
		$this->add_column('num_supplier');
		$this->add_column('num_author');
		$this->add_column('event_date');
		$this->add_column('diffusion_date');
		$this->add_column('rights_date');
		$this->add_column('request_status');
		$this->add_column('state_icon');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_account';
	        case 'date':
	        case 'title' :
	        case 'event_date' :
	        case 'event_formation' :
	        case 'event_orchestra' :
	        case 'event_place' :
	        case 'receipt_limit_date' :
	        case 'receipt_effective_date' :
	        case 'return_date' :
	        case 'diffusion_date' :
	        case 'rights_date' :
	            return 'account_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('event_date', 'align', 'center');
		$this->set_setting_column('receipt_limit_date', 'align', 'center');
		$this->set_setting_column('receipt_effective_date', 'align', 'center');
		$this->set_setting_column('return_date', 'align', 'center');
		$this->set_setting_column('id', 'datatype', 'integer');
		$this->set_setting_column('event_date', 'datatype', 'date');
		$this->set_setting_column('diffusion_date', 'datatype', 'date');
		$this->set_setting_column('rights_date', 'datatype', 'date');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('request_type');
		$this->set_filter_from_form('num_pricing_system', 'integer');
		$this->set_filter_from_form('web');
		$this->set_filter_from_form('event_date_start');
		$this->set_filter_from_form('event_date_end');
		$this->set_filter_from_form('diffusion_date_start');
		$this->set_filter_from_form('diffusion_date_end');
		$this->set_filter_from_form('rights_date_start');
		$this->set_filter_from_form('rights_date_end');
		$this->set_filter_from_form('unlimited_rights');
		$this->set_filter_from_form('invoiced', 'integer');
		$this->set_filter_from_form('request_status', 'integer');
		$this->set_filter_from_form('title');
		$this->set_filter_from_form('comment');
		$this->set_filter_from_form('pricing_system_except');
		$this->set_filter_from_form('pricing_systems');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_event_date() {
		return $this->get_search_filter_interval_date('event_date');
	}
	
	protected function get_search_filter_diffusion_date() {
	    return $this->get_search_filter_interval_date('diffusion_date');
	}
	
	protected function get_search_filter_rights_date() {
	    return $this->get_search_filter_interval_date('rights_date');
	}
	
	protected function get_search_filter_unlimited_rights() {
	    global $msg;
	    
	    return $this->get_search_filter_boolean_selection('unlimited_rights', $msg['all']);
	}
	
	protected function get_search_filter_invoiced() {
		global $msg;
		
		$options = array(
				0 => $msg['acquisition_account_type_select_all'],
				1 => $msg['acquisition_account_not_invoiced'],
				2 => $msg['acquisition_account_invoiced'],
		);
		return $this->get_search_filter_simple_selection('', 'invoiced', '', $options);
	}
	
	protected function get_search_filter_request_status() {
		global $msg;
		
		$options = array(
				0 => $msg['acquisition_account_type_select_all'],
				1 => $msg['acquisition_account_request_status_not_ordered'],
				2 => $msg['acquisition_account_request_status_ordered'],
				3 => $msg['acquisition_account_request_status_account'],
		);
		return $this->get_search_filter_simple_selection('', 'request_status', '', $options);
	}
	
	public function get_search_filters_form() {
	    global $msg;
	    
	    $search_filters_form = "
		<script type='text/javascript' src='javascript/ajax.js'></script>
		<script type='text/javascript' >
			var msg_acquisition_accounts_checked_empty = '".addslashes($msg['acquisition_accounts_checked_empty'])."';
            var msg_account_show_invoices_selector_title = '".addslashes($msg['account_show_invoices_selector_title'])."';
		</script>";
	    $search_filters_form .= parent::get_search_filters_form();
	    return $search_filters_form;
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('exercice', 'account_num_exercice', 'integer');
		$this->_add_query_filter_multiple_restriction('exercices', 'account_num_exercice', 'integer');
		$this->_add_query_filter_simple_restriction('request_type', 'account_request_type');
		$this->_add_query_filter_simple_restriction('type', 'account_type');
		$this->_add_query_filter_simple_restriction('title', 'account_title', 'boolean_search');
		$this->_add_query_filter_simple_restriction('num_publisher', 'account_num_publisher', 'integer');
		$this->_add_query_filter_simple_restriction('num_supplier', 'account_num_supplier', 'integer');
		$this->_add_query_filter_simple_restriction('num_author', 'account_num_author', 'integer');
		$this->_add_query_filter_simple_restriction('num_pricing_system', 'account_num_pricing_system', 'integer');
		$this->_add_query_filter_simple_restriction('web', 'account_web', 'integer');
		$this->_add_query_filter_interval_restriction('date', 'account_date', 'datetime');
		$this->_add_query_filter_interval_restriction('event_date', 'account_event_date', 'datetime');
		$this->_add_query_filter_interval_restriction('diffusion_date', 'account_diffusion_date', 'datetime');
		$this->_add_query_filter_interval_restriction('rights_date', 'account_rights_date', 'datetime');
		$this->_add_query_filter_simple_restriction('unlimited_rights', 'account_unlimited_rights', 'boolean');
		if($this->filters['invoiced']==1) {
			$this->query_filters [] = 'id_account not in(select account_invoice_num_account from rent_accounts_invoices)';
		}elseif($this->filters['invoiced']==2) {
			$this->query_filters [] = 'id_account in(select account_invoice_num_account from rent_accounts_invoices)';
		}
		$this->_add_query_filter_simple_restriction('request_status', 'account_request_status', 'integer');
		$this->_add_query_filter_simple_restriction('comment', 'account_comment', 'boolean_search');
		if($this->filters['pricing_system_except'] !== '') {
		    if($this->filters['pricing_system_except'] == 1) {
		        $this->query_filters [] = 'account_num_pricing_system = 0';
		    } elseif($this->filters['pricing_system_except'] == 0) {
		        $this->query_filters [] = 'account_num_pricing_system <> 0';
		    }
		}
		$this->_add_query_filter_multiple_restriction('pricing_systems', 'account_num_pricing_system', 'integer');
	}
	
	protected function _get_object_property_num_publisher($object) {
		if(isset($object->get_publisher()->display)) {
			return $object->get_publisher()->display;
		}
		return '';
	}
	
	protected function _get_object_property_num_supplier($object) {
		if(isset($object->get_supplier()->raison_sociale)) {
			return $object->get_supplier()->raison_sociale;
		}
		return '';
	}
	
	protected function _get_object_property_num_author($object) {
		if(isset($object->get_author()->display)) {
			return $object->get_author()->display;
		}
		return '';
	}
	
	protected function _get_object_property_num_pricing_system($object) {
	    if(!empty($object->get_pricing_system())) {
	        return $object->get_pricing_system()->get_label();
	    }
	    return '';
	}
	
	protected function _get_object_property_num_user($object) {
		return $object->get_user()->prenom.' '.$object->get_user()->nom;
	}
	
	protected function _get_object_property_request_status($object) {
		return $object->get_request_status_label();
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
		    case 'rights_date':
		        if(!empty($object->get_unlimited_rights())) {
		            $content .= "Illimit&eacute;";
		        } else {
		            $content .= parent::get_cell_content($object, $property);
		        }
		        break;
			case 'state_icon':
				$content .= $object->get_state_invoice();
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_human_event_date() {
		return $this->_get_query_human_interval_date('event_date');
	}
	
	protected function _get_query_human_diffusion_date() {
	    return $this->_get_query_human_interval_date('diffusion_date');
	}
	
	protected function _get_query_human_rights_date() {
	    return $this->_get_query_human_interval_date('rights_date');
	}
	
	protected function _get_query_human_invoiced() {
		global $msg;
		if($this->filters['invoiced']==2) {
			return $msg['acquisition_account_invoiced'];
		}elseif($this->filters['invoiced']==1) {
			return $msg['acquisition_account_not_invoiced'];
		}
		return '';
	}
	
	protected function _get_query_human_request_status() {
		global $msg;
		if($this->filters['request_status']) {
			switch ($this->filters['request_status']) {
				case 1 :
					return $msg['acquisition_account_request_status_not_ordered'];
				case 2 :
					return $msg['acquisition_account_request_status_ordered'];
				case 3 :
					return $msg['acquisition_account_request_status_account'];
			}
			return '';
		}
	}

	protected function _get_query_human_unlimited_rights() {
	    global $msg;
	    
	    if($this->filters['unlimited_rights'] != '' && $this->filters['unlimited_rights'] != -1) {
	        if($this->filters['unlimited_rights']) {
	            return $msg['40'];
	        } else {
	            return $msg['39'];
	        }
	    }
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = $this->_get_query_human_main_fields();
		
		if($this->filters['request_type']) {
			$account_request_types = new marc_list('rent_request_type');
			$humans[] = "<b>".htmlentities($msg['acquisition_account_request_type_name'], ENT_QUOTES, $charset)."</b> ".$account_request_types->table[$this->filters['request_type']];
		}
		if($this->filters['web']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_web'], ENT_QUOTES, $charset)."</b> ".$msg['acquisition_account_web_yes'];
		}
		return $this->get_display_query_human($humans);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
	    global $base_path, $id_bibli;
		
		$attributes = array();
		if ($property != 'state_icon') {
            if($object->is_editable()) {
                $attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id_bibli=".$id_bibli."&id=".$object->get_id()."\"";
            } else {
                if($object->get_num_invoice()) {
                    $link_edit_invoice = $base_path."/acquisition.php?categ=rent&sub=invoices&action=edit&id_bibli=&id=".$object->get_num_invoice();
                    $invoice=new rent_invoice($object->get_num_invoice());
                    if($invoice->get_status() == 1) {
                        $attributes['onclick'] = "window.location=\"".$link_edit_invoice."\"";
                    } elseif($invoice->get_status() == 2) {
                        $attributes['onclick'] = "window.location=\"".$link_edit_invoice."\"";
                    }
                }
            }
		}
		return $attributes;
	}
	
	protected function init_default_selection_actions() {
		global $msg, $base_path;
		
		parent::init_default_selection_actions();
		$invoices_link = array(
				'href' => $base_path."/acquisition.php?categ=rent&sub=invoices&action=create_from_accounts"
		);
		$this->add_selection_action('gen_invoices', $msg['acquisition_account_gen_invoices'], '', $invoices_link);
	}
	
	public function has_rights() {
	    if (static::class == 'list_rent_accounts_ui' && !(SESSrights & ACQUISITION_ACCOUNT_INVOICE_AUTH)) {
	        return false;
	    }
	    return parent::has_rights();
	}
}