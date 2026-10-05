<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_requests_ui.class.php,v 1.9 2024/04/04 07:22:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_rent_requests_ui extends list_rent_accounts_ui {
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
		$this->add_selected_filter('exercices');
		$this->add_selected_filter('request_type');
		$this->add_selected_filter('num_publisher');
		$this->add_selected_filter('num_supplier');
		$this->add_selected_filter('num_author');
		$this->add_selected_filter('event_date');
		$this->add_selected_filter('request_status');
		$this->add_selected_filter('date');
		$this->add_selected_filter('title');
		$this->add_selected_filter('comment');
	}
	
	protected function get_button_add() {
	    global $msg;
	    
	    return $this->get_button('edit', $msg['acquisition_new_request'], '&id=0');
	}
	
	protected function get_search_filter_request_type() {
		global $msg;
		$request_types = new marc_select('rent_request_type', $this->objects_type.'_request_type', $this->filters['request_type'], '', 0, $msg['acquisition_account_type_select_all']);
		return $request_types->display;
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('id');
		$this->add_column('num_user');
		$this->add_column('request_type_name');
		$this->add_column('date');
		$this->add_column('title');
		$this->add_column('num_publisher');
		$this->add_column('num_supplier');
		$this->add_column('num_author');
		$this->add_column('event_date');
		$this->add_column('receipt_limit_date');
		$this->add_column('receipt_effective_date');
		$this->add_column('return_date');
		$this->add_column('request_status');
	}
	
	protected function init_default_selection_actions() {
		global $msg, $base_path;
		
		parent::init_default_selection_actions();
		$commands_link = array(
				'openPopUp' => $base_path."/pdf.php?pdfdoc=account_command",
				'openPopUpTitle' => 'lettre'
		);
		$this->add_selection_action('gen_commands', $msg['acquisition_account_gen_commands'], '', $commands_link);
	}
}