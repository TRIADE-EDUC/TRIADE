<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_pnb_ui.class.php,v 1.31 2024/03/07 11:51:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path.'/templates/list/list_pnb_ui.tpl.php');

class list_pnb_ui extends list_ui {

	protected function _get_query_base() {
		$query = 'select id_pnb_order from pnb_orders';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new pnb_order($row->id_pnb_order);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
		    'alert_end_offers' => '',
		    'alert_staturation_offers' => '',
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'order_id' => 'edit_pnb_order_id',
					'line_id' => 'edit_pnb_order_line_id',
					'notice' => 'edit_pnb_order_notice',
					'loan_max_duration' => 'edit_pnb_order_loan_max_duration',
					'nb_loans' => 'edit_pnb_order_nb_loans',
					'nb_simultaneous_loans' => 'edit_pnb_order_nb_simultaneous_loans',
					'nb_consult_in_situ' => 'edit_pnb_order_nb_consult_in_situ',
					'nb_consult_ex_situ' => 'edit_pnb_order_nb_consult_ex_situ',
					'offer_date' => 'edit_pnb_order_offer_date',
					'offer_date_end' => 'edit_pnb_order_offer_date_end',
					'offer_duration' => 'edit_pnb_order_offer_duration',
			)
		);
		
	}
	
	protected function init_default_columns() {
		$this->add_column('order_id');
		$this->add_column('line_id');
		$this->add_column('notice');
		$this->add_column('loan_max_duration');
		$this->add_column('nb_loans');
		$this->add_column('nb_simultaneous_loans');
		$this->add_column('nb_consult_in_situ');
		$this->add_column('nb_consult_ex_situ');
		$this->add_column('offer_date');
		$this->add_column('offer_date_end');
		$this->add_column('offer_duration');
		$this->add_column_sel_button();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('loan_max_duration', 'datatype', 'integer');
		$this->set_setting_column('nb_loans', 'datatype', 'integer');
		$this->set_setting_column('nb_simultaneous_loans', 'datatype', 'integer');
		$this->set_setting_column('nb_consult_in_situ', 'datatype', 'integer');
		$this->set_setting_column('nb_consult_ex_situ', 'datatype', 'integer');
		$this->set_setting_column('offer_date', 'datatype', 'datetime');
		$this->set_setting_column('offer_date_end', 'datatype', 'datetime');
		$this->set_setting_column('offer_duration', 'datatype', 'integer');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('offer_date', 'desc');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'order_id':
	            return 'pnb_order_id_order';
	        case 'line_id':
	        case 'loan_max_duration':
	        case 'nb_loans':
	        case 'nb_simultaneous_loans':
	        case 'nb_consult_in_situ':
	        case 'nb_consult_ex_situ':
	        case 'offer_date':
	        case 'offer_date_end':
	        case 'offer_duration':
	            return 'pnb_order_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	    global $alert_end_offers, $alert_staturation_offers;
	
		if (isset($alert_end_offers)) {
			$this->filters['alert_end_offers'] = $alert_end_offers;
		} else {			
			$this->filters['alert_end_offers'] = '';
		}
		if (isset($alert_staturation_offers)) {
		    $this->filters['alert_staturation_offers'] = $alert_staturation_offers;
		} else {
		    $this->filters['alert_staturation_offers'] = '';
		}
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		global $pmb_pnb_alert_end_offers;
		
		if ($this->filters['alert_end_offers']) {
			$this->query_filters [] = " DATE_ADD(pnb_order_offer_date_end, INTERVAL - " . $pmb_pnb_alert_end_offers . " DAY) < NOW() ";
		}
		if ($this->filters['alert_staturation_offers']) {
			$this->query_filters [] = " DATE_ADD(pnb_order_offer_date_end, INTERVAL - " . $pmb_pnb_alert_end_offers . " DAY) < NOW() ";
		}
	}
	
	protected function fetch_data() {
	    $this->objects = array();
	    $query = $this->_get_query_base();
	    $query .= $this->_get_query_filters();
	    
	    if ($this->filters['alert_staturation_offers']) {
	        global $pmb_pnb_alert_staturation_offers;
	        $query = "select id_pnb_order from (select * from pnb_orders
	        join pnb_loans on pnb_loan_order_line_id = pnb_order_line_id
	        group by pnb_order_line_id having count(id_pnb_loan) >= pnb_order_nb_simultaneous_loans - " . $pmb_pnb_alert_staturation_offers . " ) as t";	        
	    }
	    
	    $query .= $this->_get_query_order();
	    if ($this->applied_sort_type == "SQL"){
	        $this->pager['nb_results'] = pmb_mysql_num_rows(pmb_mysql_query($query));
	        $query .= $this->_get_query_pager();
	    }
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while($row = pmb_mysql_fetch_object($result)) {
	            $this->add_object($row);
	        }
	        if ($this->applied_sort_type != "SQL"){
	            $this->pager['nb_results'] = pmb_mysql_num_rows($result);
	        }
	    }
	    $this->messages = "";
	    
	    $this->init_dilicom();
	}
	
	/**
	 * Initialise les données Dilicom.
	 *
	 * Cette fonction récupère les identifiants de ligne à partir du tableau d'objets et appelle l'API Dilicom pour chaque groupe de 10 identifiants de ligne.
	 * Si des identifiants de ligne restent, la fonction appelle également l'API Dilicom pour ces identifiants.
	 *
	 */
	protected function init_dilicom() {
	    global $dbh;
	    
	    $line_ids = array();
	    if(!empty($this->objects)) {
    	    foreach ($this->objects as $object) {
    	        $line_ids[] = $object->get_line_id();
    	        if(count($line_ids) === 10) {
    	            $this->call_dilicom($line_ids);
    	            $line_ids = [];
    	        }
    	    }
    	    
    	    if(count($line_ids)) {
    	        $this->call_dilicom($line_ids);
    	    }
    	    if(empty($dbh)) {
    	        //L'interrogation Dilicom peut prendre du temps
    	        //Verifions que nous n'avons pas perdu la connexion
    	        $dbh = connection_mysql();
    	    }
	    }
	}
	
	/**
	 * Appelle l'API Dilicom pour obtenir le statut de prêt des IDs spécifiés.
	 *
	 * @param array $line_ids Les IDs des lignes de commandes pour lesquelles obtenir le statut de prêt.
	 * @return void
	 */
	protected function call_dilicom($line_ids) {
	    $response = dilicom::get_instance()->get_loan_status($line_ids);
	    if(is_array($response) && count($response["loanResponseLine"])) {
	        foreach ($response["loanResponseLine"] as $response_line) {
	            pnb_order::$loans_infos[$response_line["orderLineId"]] = $response_line;
	        }
	    }
	}
	
	protected function _get_object_property_nb_loans($object) {
	    return $object->get_loans_completed_number();
	}
	
	protected function _get_object_property_nb_simultaneous_loans($object) {
	    return $object->get_loans_in_progress();
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $sub;		
		global $list_pnb_ui_script_case_a_cocher;
		
		$display = parent::get_js_sort_script_sort();
		$display.= $list_pnb_ui_script_case_a_cocher;
		$display = str_replace('!!categ!!', 'pnb', $display);
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
		    case 'notice':
		        $content .= $object->get_notice(); //conservation de l'interprétation du HTML
		        break;
			case 'nb_loans':
			    $content.=  $object->get_loans_completed_number() . " / " . parent::get_cell_content($object, $property);
				break;
			case 'nb_simultaneous_loans':
			    $content.=  $object->get_loans_in_progress() . " / " . parent::get_cell_content($object, $property);
			    break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}		
		return $content;
	}
	
	protected function get_edition_link() {
		return '';
	}
		
	protected function add_column_sel_button() {
		$this->columns[] = array(
				'property' => '',
		    'label' => "<div class='center'><input type='button' id='check_all_command_lines' class='bouton' name='+' value='+'></div>",
		    'html' => "<div class='center'><input type='checkbox' data-pnb name='sel_!!id!!' value='!!id!!'></div>",
		    'exportable' => false
		);
	}
		
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if ($this->filters['alert_end_offers']) {
			$humans[] = "<b>".htmlentities($msg['pnb_edit_end_offers_filter'], ENT_QUOTES, $charset)."</b> ";
		}
		if ($this->filters['alert_staturation_offers']) {
		    $humans[] = "<b>".htmlentities($msg['pnb_edit_staturation_offers_filter'], ENT_QUOTES, $charset)."</b> ";
		}
		$human_query = "<div class='align_left'><br />".implode(', ', $humans)." => ".sprintf(htmlentities($msg['searcher_results'], ENT_QUOTES, $charset), $this->pager['nb_results'])."<br /><br /></div>";
		return $human_query;
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $pnb_ui_search_filters_form_tpl;
		
		$search_filters_form = $pnb_ui_search_filters_form_tpl;			
		$search_filters_form = str_replace('!!alert_end_offers_checked!!', ($this->filters['alert_end_offers'] ? 'checked=checked' : '' ), $search_filters_form);
		$search_filters_form = str_replace('!!alert_staturation_offers_checked!!', ($this->filters['alert_staturation_offers'] ? 'checked=checked' : '' ), $search_filters_form);
		
		return $search_filters_form;
	}
}